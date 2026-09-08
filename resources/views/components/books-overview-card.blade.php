@props(['books'])

@php
    $books = collect($books);
    $availableCount = 0;
    $issuedCount = 0;
    $reservedCount = 0;
    $lostDamagedCount = 0;
    $unknownCount = 0;

    if ($books->isNotEmpty() && isset($books->first()->status)) {
        foreach ($books as $book) {
            $status = trim((string) ($book->status ?? ''));
            if (strcasecmp($status, 'Available') === 0) {
                $availableCount++;
            } elseif (strcasecmp($status, 'Borrowed') === 0 || strcasecmp($status, 'Issued') === 0) {
                $issuedCount++;
            } elseif (strcasecmp($status, 'Reserved') === 0) {
                $reservedCount++;
            } elseif (in_array(strtolower($status), ['lost', 'damaged'], true)) {
                $lostDamagedCount++;
            } else {
                $unknownCount++;
            }
        }
    } elseif ($books->isNotEmpty() && isset($books->first()->available)) {
        $availableCount = $books->sum(fn ($book) => max(0, intval($book->available ?? 0)));
        $issuedCount = max(0, $books->count() - $availableCount);
    } else {
        $availableCount = $books->count();
    }

    $overviewTotal = max($books->count(), 1);
    $availablePct = round(($availableCount / $overviewTotal) * 100, 1);
    $issuedPct = round(($issuedCount / $overviewTotal) * 100, 1);
    $reservedPct = round(($reservedCount / $overviewTotal) * 100, 1);
    $lostPct = round(($lostDamagedCount / $overviewTotal) * 100, 1);
    $knownPct = $availablePct + $issuedPct + $reservedPct + $lostPct;
    $otherPct = max(0, round(100 - $knownPct, 1));
    $segments = [];
    $current = 0;
    if ($availablePct > 0) {
        $segments[] = "#3b82f6 {$current}% " . ($current + $availablePct) . "%";
        $current += $availablePct;
    }
    if ($issuedPct > 0) {
        $segments[] = "#22c55e {$current}% " . ($current + $issuedPct) . "%";
        $current += $issuedPct;
    }
    if ($reservedPct > 0) {
        $segments[] = "#f59e0b {$current}% " . ($current + $reservedPct) . "%";
        $current += $reservedPct;
    }
    if ($lostPct > 0) {
        $segments[] = "#ef4444 {$current}% " . ($current + $lostPct) . "%";
        $current += $lostPct;
    }
    if ($otherPct > 0 || empty($segments)) {
        $segments[] = "#e2e8f0 {$current}% 100%";
    }
    $donutBackground = implode(', ', $segments);
@endphp

<div class="books-overview-card">
  <div class="overview-chart">
    <div class="overview-donut" style="background: conic-gradient({{ $donutBackground }});">
      <div class="overview-donut-inner">
        <div class="overview-total">{{ number_format($books->count()) }}</div>
        <div class="overview-text">Total Books</div>
      </div>
    </div>
  </div>
  <div class="overview-meta">
    <div class="overview-heading">Books Overview</div>
    <div class="overview-subtitle">Current collection status</div>
    <div class="overview-list">
      @foreach([
        ['label' => 'Available', 'count' => $availableCount, 'pct' => $availablePct, 'color' => 'blue'],
        ['label' => 'Issued', 'count' => $issuedCount, 'pct' => $issuedPct, 'color' => 'green'],
        ['label' => 'Reserved', 'count' => $reservedCount, 'pct' => $reservedPct, 'color' => 'gold'],
        ['label' => 'Lost / Damaged', 'count' => $lostDamagedCount, 'pct' => $lostPct, 'color' => 'red'],
      ] as $item)
        <div class="overview-item">
          <div style="display:flex; align-items:center; gap:12px;">
            <span class="overview-dot {{ $item['color'] }}"></span>
            <div class="overview-item-label">{{ $item['label'] }}</div>
          </div>
          <div class="overview-item-value"><strong>{{ number_format($item['count']) }}</strong> <span>({{ $item['pct'] }}%)</span></div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<style>
.books-overview-card {
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 50px;
  align-items:center;
  width: 100%;
  max-width: 100%;
}
.overview-chart {
  display: auto;
  place-items: center;
}
.overview-donut {
  width: 190px;
  height: 190px;
  border-radius: 50%;
  position: relative;
  display: grid;
  place-items: center;
  overflow: hidden;
}
.overview-donut::before {
  content: '';
  position: absolute;
  width: 108px;
  height: 108px;
  border-radius: 50%;
  background: #ffffff;
  box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04);
}
.overview-donut-inner {
  position: relative;
  text-align: center;
  z-index: 1;
  max-width: 108px;
}
.overview-total {
  font-size: 34px;
  font-weight: 800;
  color: #0f172a;
}
.overview-text {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}
.overview-meta {
  display: grid;
  gap: 8px;
}
.overview-heading {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
}
.overview-subtitle {
  color: #475569;
  font-size: 14px;
}
.overview-list {
  display: grid;
  gap: 10px;
}
.overview-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.overview-dot {
  width: 10px;
  height: 10px;
  border-radius: 40%;
  flex-shrink: 0;
}
.overview-dot.blue { background: #3b82f6; }
.overview-dot.green { background: #22c55e; }
.overview-dot.gold { background: #f59e0b; }
.overview-dot.red { background: #ef4444; }
.overview-item-label {
  font-size: 14px;
  color: #0f172a;
  font-weight: 600;
}
.overview-item-value {
  color: #475569;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 100px;
  justify-content: flex-end;
}
.overview-item-value span {
  color: #94a3b8;
  font-weight: 600;
}
@media (max-width: 900px) {
  .books-overview-card { grid-template-columns: 1fr; }
  .overview-donut { width: 100%; height: 200px; }
}
</style>
