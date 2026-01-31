@props(['type' => 'info', 'message' => '', 'route' => '#'])

<div class="p-4 rounded shadow
    @if($type === 'warning') bg-yellow-100 text-yellow-800
    @elseif($type === 'danger') bg-red-100 text-red-800
    @else bg-blue-100 text-blue-800
    @endif
">
    <p>{{ $message }}</p>
    @if($route)
        <a href="{{ route($route) }}" class="underline text-sm">Voir</a>
    @endif
</div>
