@props(['label' => '', 'value' => '-', 'href' => null])

@if ($href)
  <a href="{{ $href }}" class="block rounded-xl border border-gray-200 bg-white p-5 shadow hover:shadow-md transition">
    <div class="text-gray-500 text-sm">{{ $label }}</div>
    <div class="mt-2 text-2xl font-bold">{{ $value }}</div>
    <div class="mt-3 text-indigo-600 text-sm">View →</div>
  </a>
@else
  <div class="rounded-xl border border-gray-200 bg-white p-5 shadow">
    <div class="text-gray-500 text-sm">{{ $label }}</div>
    <div class="mt-2 text-2xl font-bold">{{ $value }}</div>
  </div>
@endif
