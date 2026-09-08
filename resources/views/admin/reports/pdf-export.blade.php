<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Library Report — {{ $generatedAt }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px; }
    .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1e293b; padding-bottom: 12px; }
    .header h2 { margin: 0 0 4px; font-size: 18px; }
    .header p { margin: 0; color: #64748b; font-size: 10px; }
    .section { margin-bottom: 28px; page-break-inside: avoid; }
    .section-title { font-size: 14px; font-weight: 700; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #cbd5e1; color: #0f172a; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    th { background: #f1f5f9; font-weight: 700; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 10px; text-transform: uppercase; letter-spacing: 0.03em; }
    td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 10px; vertical-align: top; word-wrap: break-word; }
    tr:nth-child(even) td { background: #f8fafc; }
    .empty { text-align: center; color: #94a3b8; padding: 20px; font-style: italic; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
  </style>
</head>
<body>

<div class="header">
  <h2>Library Management System — Report</h2>
  <p>Generated: {{ $generatedAt }} | Categories: {{ implode(', ', array_map('ucfirst', $categories)) }}</p>
</div>

{{-- ===== PROPOSALS ===== --}}
@if(in_array('proposals', $categories))
<div class="section">
  <div class="section-title">Research Proposals</div>
  @if(!empty($proposalRows) && count($proposalRows))
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Titles of Proposal</th>
        <th>Date of Submitted</th>
        <th>Deadline</th>
        <th>Funds</th>
      </tr>
    </thead>
    <tbody>
      @foreach($proposalRows as $p)
      <tr>
        <td>{{ $p->researcher_name ?: '—' }}</td>
        <td>{{ $p->title ?: '—' }}</td>
        <td>{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('M d, Y') : '—' }}</td>
        <td>{{ $p->deadline ? \Carbon\Carbon::parse($p->deadline)->format('M d, Y') : '—' }}</td>
        <td class="text-right">₱{{ number_format($p->budget ?? 0, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No proposal data available.</div>
  @endif
</div>
@endif

{{-- ===== BORROWING ===== --}}
@if(in_array('borrowing', $categories))
<div class="section">
  <div class="section-title">Book Borrowing Report</div>
  @if(!empty($borrowingRows) && count($borrowingRows))
  <table>
    <thead>
      <tr>
        <th>Name of Borrower</th>
        <th>Category</th>
        <th>Title of Books</th>
        <th>Author</th>
        <th>Date of Request</th>
        <th>Date of Deadline</th>
        <th>Return</th>
        <th>Fines</th>
      </tr>
    </thead>
    <tbody>
      @foreach($borrowingRows as $b)
      <tr>
        <td>{{ $b->borrower_name ?: '—' }}</td>
        <td>{{ $b->book_category ?: '—' }}</td>
        <td>{{ $b->book_title ?: '—' }}</td>
        <td>{{ $b->book_author ?: '—' }}</td>
        <td>{{ $b->borrow_date ? \Carbon\Carbon::parse($b->borrow_date)->format('M d, Y') : '—' }}</td>
        <td>{{ $b->due_date ? \Carbon\Carbon::parse($b->due_date)->format('M d, Y') : '—' }}</td>
        <td>{{ $b->returned_at ? \Carbon\Carbon::parse($b->returned_at)->format('M d, Y') : 'Not Returned' }}</td>
        <td class="text-right">{{ $b->fine_amount > 0 ? '₱' . number_format($b->fine_amount, 2) : '—' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No borrowing data available.</div>
  @endif
</div>
@endif

{{-- ===== SCHOOLS ===== --}}
@if(in_array('schools', $categories))
<div class="section">
  <div class="section-title">Academic Affiliation Distribution</div>
  @if(!empty($schoolRows) && count($schoolRows))
  <table>
    <thead>
      <tr><th>School Name</th><th class="text-center">User Count</th><th class="text-center">Percentage</th></tr>
    </thead>
    <tbody>
      @foreach($schoolRows as $s)
      @php $spct = ($totalSchoolUsers ?? 0) > 0 ? round(($s->count / $totalSchoolUsers) * 100) : 0; @endphp
      <tr>
        <td>{{ $s->school ?: 'Unspecified' }}</td>
        <td class="text-center">{{ $s->count }}</td>
        <td class="text-center">{{ $spct }}%</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No school distribution data available.</div>
  @endif
</div>
@endif

{{-- ===== AGES ===== --}}
@if(in_array('ages', $categories))
<div class="section">
  <div class="section-title">Age Bracket Ranges</div>
  @if(!empty($ageRows))
  <table>
    <thead>
      <tr><th>Age Bracket</th><th class="text-center">Count</th><th class="text-center">Percentage</th></tr>
    </thead>
    <tbody>
      @php $ageTotal = max(1, array_sum($ageRows)); @endphp
      @foreach($ageRows as $label => $count)
      <tr>
        <td>{{ $label }}</td>
        <td class="text-center">{{ $count }}</td>
        <td class="text-center">{{ round(($count / $ageTotal) * 100) }}%</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No age bracket data available.</div>
  @endif
</div>
@endif

{{-- ===== TOP USERS ===== --}}
@if(in_array('topusers', $categories))
<div class="section">
  <div class="section-title">Top Frequent Library Visitors</div>
  @if(!empty($topVisitorRows) && count($topVisitorRows))
  <table>
    <thead>
      <tr><th class="text-center">Rank</th><th>Visitor Name</th><th>Barcode</th><th class="text-center">Visit Count</th></tr>
    </thead>
    <tbody>
      @foreach($topVisitorRows as $i => $v)
      <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>{{ $v->full_name ?: 'Unknown' }}</td>
        <td>{{ $v->barcode_id ?: 'N/A' }}</td>
        <td class="text-center">{{ $v->visit_count ?? 0 }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No visitor ranking data available.</div>
  @endif
</div>
@endif

{{-- ===== TOP WIFI USERS ===== --}}
@if(in_array('topwifi', $categories))
<div class="section">
  <div class="section-title">Top WiFi Users</div>
  @if(!empty($topWifiRows) && count($topWifiRows))
  <table>
    <thead>
      <tr><th class="text-center">Rank</th><th>User</th><th>User ID</th><th>Bandwidth (GB)</th><th>Voucher</th></tr>
    </thead>
    <tbody>
      @foreach($topWifiRows as $i => $w)
      <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>{{ $w->name ?: 'Unknown' }}</td>
        <td>{{ $w->user_id ?: 'N/A' }}</td>
        <td class="text-center">{{ $w->bandwidth_gb ?? 0 }}</td>
        <td>{{ $w->voucher_code ?: 'N/A' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No WiFi usage data available.</div>
  @endif
</div>
@endif

{{-- ===== GENDER DEMOGRAPHICS ===== --}}
@if(in_array('genders', $categories))
<div class="section">
  <div class="section-title">User Gender Demographics</div>
  <table>
    <thead>
      <tr><th>Gender</th><th class="text-center">Count</th><th class="text-center">Percentage</th></tr>
    </thead>
    <tbody>
      <tr><td>Female</td><td class="text-center">{{ $femaleCount ?? 0 }}</td><td class="text-center">{{ $femalePct ?? 0 }}%</td></tr>
      <tr><td>Male</td><td class="text-center">{{ $maleCount ?? 0 }}</td><td class="text-center">{{ $malePct ?? 0 }}%</td></tr>
    </tbody>
  </table>
</div>
@endif

{{-- ===== GATE CHECK-INS ===== --}}
@if(in_array('gatecheckins', $categories))
<div class="section">
  <div class="section-title">Gate Access Terminal Logs</div>
  @if(!empty($gateRows) && count($gateRows))
  <table>
    <thead>
      <tr><th>Patron</th><th>Barcode</th><th>Check-In</th><th>Check-Out</th></tr>
    </thead>
    <tbody>
      @foreach($gateRows as $g)
      <tr>
        <td>{{ $g->full_name ?: 'Unknown Member' }}</td>
        <td>{{ $g->barcode_id ?: 'N/A' }}</td>
        <td>{{ $g->entry_time ? \Carbon\Carbon::parse($g->entry_time)->format('M d, Y h:i A') : 'N/A' }}</td>
        <td>{{ $g->exit_time ? \Carbon\Carbon::parse($g->exit_time)->format('M d, Y h:i A') : 'Active' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty">No gate check-in data available.</div>
  @endif
</div>
@endif

<div class="footer">
  Library Management System — Report generated {{ $generatedAt }}
</div>

</body>
</html>
