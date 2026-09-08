@props(['title' => null, 'subtitle' => null])
<section {{ $attributes->merge(['class' => 'card']) }}>
  @if($title || $subtitle || isset($actions))
    <div class="card-header">
      <div>
        @if($title)<div class="card-title">{{ $title }}</div>@endif
        @if($subtitle)<div class="card-sub">{{ $subtitle }}</div>@endif
      </div>
      @isset($actions){{ $actions }}@endisset
    </div>
  @endif
  <div class="card-body">{{ $slot }}</div>
</section>
