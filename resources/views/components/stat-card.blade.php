@props(['label', 'value', 'change' => null, 'tone' => 'primary', 'icon' => null])
<div class="stat-card {{ $tone }}">
  @if($icon)
    <div class="stat-icon">{{ $icon }}</div>
  @endif
  <div class="stat-label">{{ $label }}</div>
  <div class="stat-value">{{ $value }}</div>
  @if($change)
    <div class="stat-change">{{ $change }}</div>
  @endif

</div>
