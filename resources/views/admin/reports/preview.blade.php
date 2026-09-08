<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Library Report Preview — {{ $generatedAt }}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 13px; color: #1e293b; background: #f1f5f9; }
    .preview-toolbar { position: sticky; top: 0; z-index: 10; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 12px 24px; gap: 16px; flex-wrap: wrap; }
    .preview-toolbar h3 { font-size: 15px; font-weight: 600; }
    .toolbar-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn { display: inline-block; padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
    .btn-pdf { background: #dc2626; color: #fff; }
    .btn-pdf:hover { background: #b91c1c; }
    .btn-back { background: #334155; color: #fff; }
    .btn-back:hover { background: #1e293b; }
    .btn-print { background: #2563EB; color: #fff; }
    .btn-print:hover { background: #1d4ed8; }
    .preview-container { max-width: 1000px; margin: 24px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 40px; }
    .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1e293b; padding-bottom: 12px; }
    .header h2 { margin: 0 0 4px; font-size: 20px; }
    .header p { margin: 0; color: #64748b; font-size: 11px; }
    .section { margin-bottom: 28px; }
    .section-title { font-size: 15px; font-weight: 700; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #cbd5e1; color: #0f172a; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    th { background: #f1f5f9; font-weight: 700; text-align: left; padding: 7px 10px; border: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; letter-spacing: 0.03em; }
    td { padding: 6px 10px; border: 1px solid #e2e8f0; font-size: 12px; vertical-align: top; word-wrap: break-word; }
    tr:nth-child(even) td { background: #f8fafc; }
    .empty { text-align: center; color: #94a3b8; padding: 20px; font-style: italic; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    {{-- Hide toolbar when printing --}}
    @media print {
      .preview-toolbar { display: none !important; }
      body { background: #fff; }
      .preview-container { box-shadow: none; margin: 0; padding: 16px; max-width: 100%; border-radius: 0; }
    }
  </style>
</head>
<body>

<div class="preview-toolbar">
  <div>
    <h3>📊 Report Preview</h3>
    <span style="font-size:11px; opacity:0.7;">{{ $generatedAt }} · {{ implode(', ', array_map('ucfirst', $categories)) }}</span>
  </div>
  <div class="toolbar-actions">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print</button>
    <a class="btn btn-pdf" href="{{ route('admin.reports.export', ['categories' => $categoriesParam]) }}">📥 Download PDF</a>
    <a class="btn btn-back" href="javascript:window.close()">✕ Close</a>
  </div>
</div>

<div class="preview-container">
  @include('admin.reports.pdf-export')
</div>

</body>
</html>
