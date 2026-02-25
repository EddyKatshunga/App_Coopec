@props([
    'title', 
    'icon' => null, 
    'route', 
    'routeParams' => [] // Paramètres optionnels de route
])

<a href="{{ route($route, $routeParams) }}" wire:navigate
   class="block p-4 bg-white rounded shadow hover:bg-blue-50 transition">
    
    @if($icon)
        <i class="icon-{{ $icon }}"></i>
    @endif
    
    <h3 class="font-semibold">{{ $title }}</h3>
</a>