<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aggregator;
use App\Models\Bank;
use App\Models\ExpenseRecipient;
use App\Models\MandatoryExpense;
use App\Models\MandatoryExpenseType;
use App\Models\Mno;
use App\Models\OperationalCategory;
use App\Models\OperationalExpense;
use App\Models\Service;
use App\Models\ServicePartnerShare;
use App\Models\ServiceType;
use App\Models\VasRevenue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    private array $ruleTypes = ['Percent_of_X', 'Percent_of_RA', 'Percent_of_RS'];
    private array $sections = [
        'services',
        'service-types',
        'mnos',
        'aggregators',
        'banks',
        'mandatory-types',
        'operational-categories',
        'recipients',
    ];

    public function index()
    {
        return view('admin.settings.index', array_merge(
            $this->sharedData(),
            ['activeSection' => null]
        ));
    }

    public function section(string $section)
    {
        abort_unless(in_array($section, $this->sections, true), 404);

        return view('admin.settings.index', array_merge(
            $this->sharedData(),
            [
                'activeSection' => $section,
                'sectionTitle' => $this->sectionTitle($section),
            ]
        ));
    }

    public function storeService(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:255',
            'service_type_id' => 'nullable|exists:service_types,id',
        ]);

        $service = Service::create([
            'name' => $data['service_name'],
            'service_type_id' => $data['service_type_id'] ?? null,
        ]);

        $service->partnerShares()->create([
            'dr_share' => env('RS_SPLIT_DR', 50),
            'aj_share' => env('RS_SPLIT_AJ', 30),
            'tj_share' => env('RS_SPLIT_TJ', 20),
            'effective_from' => now()->toDateString(),
        ]);

        return back()->with('ok', 'Service created.');
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:255',
            'service_type_id' => 'nullable|exists:service_types,id',
            'service_id' => 'nullable',
        ]);

        $service->update([
            'name' => $data['service_name'],
            'service_type_id' => $data['service_type_id'] ?? null,
        ]);

        return back()->with('ok', 'Service updated.');
    }

    public function destroyService(Service $service): RedirectResponse
    {
        if (VasRevenue::where('service_id', $service->id)->exists()) {
            return back()->with('error', 'Cannot delete a service that has revenue records.');
        }

        $service->delete();

        return back()->with('ok', 'Service deleted.');
    }

    public function storeServiceType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_type_name' => 'required|string|max:255|unique:service_types,name',
        ]);

        ServiceType::create(['name' => $data['service_type_name']]);

        return back()->with('ok', 'Service type created.');
    }

    public function updateServiceType(Request $request, ServiceType $serviceType): RedirectResponse
    {
        $data = $request->validate([
            'service_type_name' => 'required|string|max:255|unique:service_types,name,' . $serviceType->id,
            'service_type_id' => 'nullable',
        ]);

        $serviceType->update(['name' => $data['service_type_name']]);

        return back()->with('ok', 'Service type updated.');
    }

    public function destroyServiceType(ServiceType $serviceType): RedirectResponse
    {
        if (Service::where('service_type_id', $serviceType->id)->exists()) {
            return back()->with('error', 'Cannot delete a service type that is assigned to services.');
        }

        $serviceType->delete();

        return back()->with('ok', 'Service type deleted.');
    }

    public function storeServiceShare(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validateShareInput($request);
        $this->assertShareTotals($data);

        if ($service->partnerShares()
            ->whereDate('effective_from', $data['effective_from'])
            ->exists()) {
            return back()
                ->withInput()
                ->withErrors(['effective_from' => 'A share schedule already exists for that effective date.']);
        }

        $service->partnerShares()->create($data);

        return back()->with('ok', 'Founding partner share schedule added.');
    }

    public function updateServiceShare(Request $request, ServicePartnerShare $servicePartnerShare): RedirectResponse
    {
        $data = $this->validateShareInput($request);
        $this->assertShareTotals($data);

        $duplicate = ServicePartnerShare::where('service_id', $servicePartnerShare->service_id)
            ->whereDate('effective_from', $data['effective_from'])
            ->where('id', '<>', $servicePartnerShare->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['effective_from' => 'Another schedule already exists for that effective date.']);
        }

        $servicePartnerShare->update($data);

        return back()->with('ok', 'Founding partner share schedule updated.');
    }

    public function storeMno(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mno_name' => 'required|string|max:255',
        ]);

        Mno::create(['name' => $data['mno_name']]);

        return back()->with('ok', 'MNO created.');
    }

    public function updateMno(Request $request, Mno $mno): RedirectResponse
    {
        $data = $request->validate([
            'mno_name' => 'required|string|max:255',
            'mno_id' => 'nullable',
        ]);

        $mno->update(['name' => $data['mno_name']]);

        return back()->with('ok', 'MNO updated.');
    }

    public function destroyMno(Mno $mno): RedirectResponse
    {
        if (VasRevenue::where('mno_id', $mno->id)->exists()) {
            return back()->with('error', 'Cannot delete an MNO that has revenue records.');
        }

        $mno->delete();

        return back()->with('ok', 'MNO deleted.');
    }

    public function storeAggregator(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'aggregator_name' => 'required|string|max:255',
            'aggregator_short_code' => 'nullable|string|max:50',
        ]);

        Aggregator::create([
            'name' => $data['aggregator_name'],
            'short_code' => $data['aggregator_short_code'],
        ]);

        return back()->with('ok', 'Aggregator created.');
    }

    public function updateAggregator(Request $request, Aggregator $aggregator): RedirectResponse
    {
        $data = $request->validate([
            'aggregator_name' => 'required|string|max:255',
            'aggregator_short_code' => 'nullable|string|max:50',
            'aggregator_id' => 'nullable',
        ]);

        $aggregator->update([
            'name' => $data['aggregator_name'],
            'short_code' => $data['aggregator_short_code'],
        ]);

        return back()->with('ok', 'Aggregator updated.');
    }

    public function destroyAggregator(Aggregator $aggregator): RedirectResponse
    {
        if (VasRevenue::where('aggregator_id', $aggregator->id)->exists()) {
            return back()->with('error', 'Cannot delete an aggregator that has revenue records.');
        }

        $aggregator->delete();

        return back()->with('ok', 'Aggregator deleted.');
    }

    public function storeBank(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255|unique:banks,name',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_currency' => 'nullable|string|max:10',
        ]);

        Bank::create([
            'name' => $data['bank_name'],
            'account_number' => $data['bank_account_number'],
            'currency' => $data['bank_currency'] ?? 'NGN',
        ]);

        return back()->with('ok', 'Bank added.');
    }

    public function updateBank(Request $request, Bank $bank): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255|unique:banks,name,' . $bank->id,
            'bank_account_number' => 'nullable|string|max:100',
            'bank_currency' => 'nullable|string|max:10',
            'bank_id' => 'nullable',
        ]);

        $bank->update([
            'name' => $data['bank_name'],
            'account_number' => $data['bank_account_number'],
            'currency' => $data['bank_currency'] ?? 'NGN',
        ]);

        return back()->with('ok', 'Bank updated.');
    }

    public function destroyBank(Bank $bank): RedirectResponse
    {
        if ($bank->vasRevenues()->exists()) {
            return back()->with('error', 'Cannot delete a bank linked to revenue records.');
        }

        $bank->delete();

        return back()->with('ok', 'Bank removed.');
    }

    public function storeMandatoryType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mandatory_type_name' => 'required|string|max:255',
            'mandatory_type_rule' => ['required', Rule::in($this->ruleTypes)],
        ]);

        MandatoryExpenseType::create([
            'name' => $data['mandatory_type_name'],
            'rule_type' => $data['mandatory_type_rule'],
        ]);

        return back()->with('ok', 'Mandatory expense type created.');
    }

    public function updateMandatoryType(Request $request, MandatoryExpenseType $mandatoryExpenseType): RedirectResponse
    {
        $data = $request->validate([
            'mandatory_type_name' => 'required|string|max:255',
            'mandatory_type_rule' => ['required', Rule::in($this->ruleTypes)],
            'mandatory_type_id' => 'nullable',
        ]);

        $mandatoryExpenseType->update([
            'name' => $data['mandatory_type_name'],
            'rule_type' => $data['mandatory_type_rule'],
        ]);

        return back()->with('ok', 'Mandatory expense type updated.');
    }

    public function destroyMandatoryType(MandatoryExpenseType $mandatoryExpenseType): RedirectResponse
    {
        if (MandatoryExpense::where('mandatory_expense_type_id', $mandatoryExpenseType->id)->exists()) {
            return back()->with('error', 'Cannot delete a type that is attached to expenses.');
        }

        $mandatoryExpenseType->delete();

        return back()->with('ok', 'Mandatory expense type deleted.');
    }

    public function storeOperationalCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'operational_category_name' => 'required|string|max:255',
        ]);

        OperationalCategory::create(['name' => $data['operational_category_name']]);

        return back()->with('ok', 'Operational category created.');
    }

    public function updateOperationalCategory(Request $request, OperationalCategory $operationalCategory): RedirectResponse
    {
        $data = $request->validate([
            'operational_category_name' => 'required|string|max:255',
            'operational_category_id' => 'nullable',
        ]);

        $operationalCategory->update(['name' => $data['operational_category_name']]);

        return back()->with('ok', 'Operational category updated.');
    }

    public function destroyOperationalCategory(OperationalCategory $operationalCategory): RedirectResponse
    {
        if (
            OperationalExpense::where('operational_category_id', $operationalCategory->id)->exists() ||
            ExpenseRecipient::where('operational_category_id', $operationalCategory->id)->exists()
        ) {
            return back()->with('error', 'Cannot delete a category that is referenced by expenses or recipients.');
        }

        $operationalCategory->delete();

        return back()->with('ok', 'Operational category deleted.');
    }

    public function storeRecipient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_category_id' => 'required|exists:operational_categories,id',
        ]);

        ExpenseRecipient::create([
            'name' => $data['recipient_name'],
            'operational_category_id' => $data['recipient_category_id'],
        ]);

        return back()->with('ok', 'Recipient created.');
    }

    public function updateRecipient(Request $request, ExpenseRecipient $expenseRecipient): RedirectResponse
    {
        $data = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_category_id' => 'required|exists:operational_categories,id',
            'recipient_id' => 'nullable',
        ]);

        $expenseRecipient->update([
            'name' => $data['recipient_name'],
            'operational_category_id' => $data['recipient_category_id'],
        ]);

        return back()->with('ok', 'Recipient updated.');
    }

    public function destroyRecipient(ExpenseRecipient $expenseRecipient): RedirectResponse
    {
        if (OperationalExpense::where('expense_recipient_id', $expenseRecipient->id)->exists()) {
            return back()->with('error', 'Cannot delete a recipient that is referenced by operational expenses.');
        }

        $expenseRecipient->delete();

        return back()->with('ok', 'Recipient deleted.');
    }

    private function sharedData(): array
    {
        return [
            'services' => Service::with([
                'serviceType',
                'latestPartnerShare',
                'partnerShares' => fn ($query) => $query->orderByDesc('effective_from'),
            ])->orderBy('name')->get(),
            'serviceTypes' => ServiceType::orderBy('name')->get(),
            'mnos' => Mno::orderBy('name')->get(),
            'aggregators' => Aggregator::orderBy('name')->get(),
            'banks' => Bank::orderBy('name')->get(),
            'mandatoryTypes' => MandatoryExpenseType::orderBy('name')->get(),
            'operationalCategories' => OperationalCategory::orderBy('name')->get(),
            'expenseRecipients' => ExpenseRecipient::with('operationalCategory')->orderBy('name')->get(),
            'ruleTypes' => $this->ruleTypes,
        ];
    }

    private function sectionTitle(string $section): string
    {
        return match ($section) {
            'services' => 'Services',
            'service-types' => 'Service Types',
            'mnos' => 'MNOs',
            'aggregators' => 'Aggregators',
            'banks' => 'Banks',
            'mandatory-types' => 'Mandatory Expense Types',
            'operational-categories' => 'Operational Expense Categories',
            'recipients' => 'Operational Expense Recipients',
            default => ucfirst(str_replace('-', ' ', $section)),
        };
    }

    private function validateShareInput(Request $request): array
    {
        return $request->validate([
            'dr_share' => 'required|numeric|min:0|max:100',
            'aj_share' => 'required|numeric|min:0|max:100',
            'tj_share' => 'required|numeric|min:0|max:100',
            'effective_from' => 'required|date',
            'share_service_id' => 'nullable|integer',
            'share_entry_id' => 'nullable|integer',
        ]);
    }

    private function assertShareTotals(array $data): void
    {
        $total = (float) $data['dr_share'] + (float) $data['aj_share'] + (float) $data['tj_share'];
        if (abs($total - 100) > 0.01) {
            throw ValidationException::withMessages([
                'dr_share' => 'DR + AJ + TJ must equal 100%.',
            ]);
        }
    }
}


