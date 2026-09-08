<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Barcode Cards — Panabo City Library</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f3f4f6;
      color: #1f2937;
    }

    .header {
      background: white;
      padding: 20px 30px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h1 {
      font-size: 24px;
      font-weight: 700;
      margin: 0;
    }

    .header a {
      text-decoration: none;
      color: #6b7280;
      font-weight: 600;
      font-size: 14px;
    }

    .header a:hover {
      color: #1f2937;
    }

    .container {
      max-width: 1600px;
      margin: 0 auto;
      padding: 30px;
    }

    .cards-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 20px;
    }

    .barcode-card {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      transition: box-shadow 0.2s;
    }

    .barcode-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-badge {
      display: inline-block;
      font-size: 11px;
      font-weight: 700;
      padding: 6px 10px;
      border-radius: 6px;
      background: #dbeafe;
      color: #1e40af;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0 auto;
    }

    .card-title {
      font-size: 15px;
      font-weight: 700;
      color: #1f2937;
      line-height: 1.4;
    }

    .card-author {
      font-size: 13px;
      color: #6b7280;
    }

    .card-barcode-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 16px 0;
      min-height: 100px;
      background: #f9fafb;
      border-radius: 8px;
      margin: 8px 0;
    }

    .card-barcode-container img {
      max-height: 80px;
      width: auto;
      max-width: 100%;
      object-fit: contain;
    }

    .card-barcode {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .card-barcode-text {
      font-family: 'Courier New', monospace;
      font-size: 12px;
      font-weight: 700;
      color: #1f2937;
      letter-spacing: 1px;
      margin-top: 8px;
    }

    .card-footer {
      display: flex;
      flex-direction: column;
      gap: 8px;
      font-size: 12px;
      margin-top: 8px;
      padding-top: 12px;
      border-top: 1px solid #f3f4f6;
    }

    .card-date {
      color: #6b7280;
      font-size: 11px;
    }

    .card-status {
      display: inline-block;
      padding: 6px 10px;
      background: #d1fae5;
      color: #065f46;
      border-radius: 6px;
      font-weight: 700;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .print-btn {
      position: fixed;
      top: 20px;
      right: 30px;
      padding: 12px 20px;
      background: #059669;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      z-index: 100;
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .print-btn:hover {
      background: #047857;
    }

    @media (max-width: 1400px) {
      .cards-grid { grid-template-columns: repeat(4, 1fr); gap: 16px; }
    }

    @media (max-width: 1000px) {
      .cards-grid { grid-template-columns: repeat(3, 1fr); gap: 16px; }
    }

    @media (max-width: 700px) {
      .cards-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
      .container { padding: 15px; }
    }

    @media print {
      body { background: white; }
      .header, .print-btn { display: none; }
      .barcode-card { page-break-inside: avoid; box-shadow: none; border: 1px solid #ddd; }
      .container { padding: 10px; }
    }
  </style>
</head>
<body>
  <div class="header">
    <h1> Book Barcode Cards</h1>
    <a href="{{ route('staff.dashboard') }}">← Back to Dashboard</a>
  </div>

  <button class="print-btn" onclick="window.print()"> Print All</button>

  <div class="container">
    <div class="cards-grid" id="cardsContainer">
      <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #6b7280;">
        <p style="font-size: 16px;">Loading books...</p>
      </div>
    </div>
  </div>

  <script>
    function formatDate(dateStr) {
      if (!dateStr) return 'N/A';
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    }

    async function loadBooks() {
      try {
        const response = await fetch('/api/books');
        const data = await response.json();

        const container = document.getElementById('cardsContainer');

        if (!data.books || data.books.length === 0) {
          container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #6b7280;"><p style="font-size: 16px;">📚 No books found in the library</p></div>';
          return;
        }

        container.innerHTML = data.books.map(book => `
          <div class="barcode-card">
            <div class="card-badge">${escapeHtml(book.category || 'GENERAL').substring(0, 20)}</div>
            <div class="card-title">${escapeHtml(book.title)}</div>
            <div class="card-author">${escapeHtml(book.author || 'Unknown Author')}</div>
            <div class="card-barcode-container">
              <img src="/barcode/book/${encodeURIComponent(book.barcode)}" alt="Barcode for ${escapeHtml(book.barcode)}">
            </div>
            <div class="card-barcode">
              <div class="card-barcode-text">${escapeHtml(book.barcode)}</div>
            </div>
            <div class="card-footer">
              <span class="card-date">Verified ${formatDate(book.created_at)}</span>
              <span class="card-status">Barcode Ready</span>
            </div>
          </div>
        `).join('');
      } catch (error) {
        console.error('Error loading books:', error);
        document.getElementById('cardsContainer').innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #ef4444;"><p style="font-size: 16px;">⚠️ Error loading books. Please try again.</p></div>';
      }
    }

    loadBooks();
  </script>
</body>
</html>
