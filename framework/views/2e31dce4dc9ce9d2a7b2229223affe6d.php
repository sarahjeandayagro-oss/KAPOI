<?php $__env->startSection('title', 'Proposal Review - Panabo City Library'); ?>

<?php
  $selectedMime = $selected->mime_type ?? '';
  $selectedName = strtolower($selected->stored_name ?? $selected->original_name ?? '');
  $isPdf = str_contains($selectedMime, 'pdf') || str_ends_with($selectedName, '.pdf');
  $isImage = str_starts_with($selectedMime, 'image/') || preg_match('/\.(jpg|jpeg|png)$/', $selectedName);
  $selectedUrl = route('proposal.documents.view', $selected->id);
?>

<?php $__env->startSection('content'); ?>
  <div class="proposal-review-shell">
    <section class="proposal-review-document">
      <div class="proposal-review-header">
        <div>
          <div class="proposal-review-kicker">Research Proposal Review</div>
          <h1><?php echo e($proposal->title); ?></h1>
          <p><?php echo e($proposal->user_id); ?></p>
        </div>
        <div class="proposal-review-actions">
          <div class="proposal-document-name">
            <span>Viewing</span>
            <strong><?php echo e($selected->original_name); ?></strong>
          </div>
          <a id="doc-download" href="<?php echo e(route('proposal.documents.download', $selected->id)); ?>" class="btn-sm">Download</a>
        </div>
      </div>

      <div class="proposal-document-tabs">
        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a
            class="proposal-document-tab <?php echo e($doc->id === $selected->id ? 'active' : ''); ?>"
            href="<?php echo e(route('proposal.documents.view', $doc->id)); ?>"
            target="_blank"
            rel="noopener"
          >
            <?php echo e(strtoupper(str_replace('_', ' ', $doc->document_type))); ?>

          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div id="pdf-container" class="proposal-document-frame">
        <?php if($isPdf): ?>
          <div class="proposal-loading">Loading PDF preview...</div>
        <?php elseif($isImage): ?>
          <img src="<?php echo e($selectedUrl); ?>" alt="<?php echo e($selected->original_name); ?>" class="proposal-image-preview">
        <?php else: ?>
          <div class="proposal-empty-preview">
            <strong>Preview is not available for this file type.</strong>
            <span>Use Download to open the document in another application.</span>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <aside class="proposal-review-comments">
      <div class="comments-header">
        <div>
          <h2>Feedback</h2>
          <p>Click a PDF page to attach the page and position automatically.</p>
        </div>
      </div>

      <div id="comments-list" class="comments-list">
        <?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="comment-item">
            <div class="comment-meta">
              <strong><?php echo e(ucfirst($c->commenter_role ?? 'Admin')); ?></strong>
              <small><?php echo e(\Carbon\Carbon::parse($c->created_at)->diffForHumans()); ?></small>
            </div>
            <div class="comment-body">
              <?php if($c->page): ?>
                <small>Page <?php echo e($c->page); ?></small>
              <?php endif; ?>
              <div><?php echo nl2br(e($c->comment_text)); ?></div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="comment-empty">No feedback yet.</div>
        <?php endif; ?>
      </div>

      <form id="comment-form" class="comment-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
        <div class="form-row">
          <div class="form-group">
            <label>Page</label>
            <input type="number" name="page" min="1" placeholder="Optional">
          </div>
          <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" placeholder="Optional">
          </div>
        </div>
        <div class="form-group">
          <label>Comment</label>
          <textarea name="comment_text" required placeholder="Write detailed feedback for the researcher..."></textarea>
        </div>
        <button type="submit" class="btn-sm success">Add Feedback</button>
      </form>
    </aside>
  </div>

  <?php $__env->startPush('head'); ?>
    <style>
      .proposal-review-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 18px;
        min-height: calc(100vh - 128px);
      }

      .proposal-review-document,
      .proposal-review-comments {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        min-width: 0;
      }

      .proposal-review-document {
        display: flex;
        flex-direction: column;
        overflow: hidden;
      }

      .proposal-review-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
      }

      .proposal-review-kicker {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #2f9e8f;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
      }

      .proposal-review-header h1 {
        margin: 0;
        color: #0f172a;
        font-size: 20px;
        line-height: 1.25;
      }

      .proposal-review-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
      }

      .proposal-review-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 260px;
        justify-content: flex-end;
      }

      .proposal-document-name {
        display: grid;
        gap: 2px;
        max-width: 280px;
        padding: 8px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
      }

      .proposal-document-name span {
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
      }

      .proposal-document-name strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 12px;
        color: #0f172a;
      }

      .proposal-document-tabs {
        display: flex;
        gap: 8px;
        padding: 12px 20px;
        border-bottom: 1px solid #e5e7eb;
        overflow-x: auto;
      }

      .proposal-document-tab {
        flex: 0 0 auto;
        border: 1px solid #dbe3ea;
        border-radius: 8px;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        padding: 8px 10px;
        text-decoration: none;
        background: #ffffff;
      }

      .proposal-document-tab.active,
      .proposal-document-tab:hover {
        color: #0f766e;
        background: #ecfdf5;
        border-color: #99f6e4;
      }

      .proposal-document-frame {
        flex: 1;
        min-height: 72vh;
        overflow: auto;
        background: #eef2f7;
        padding: 18px;
      }

      .proposal-loading,
      .proposal-empty-preview {
        display: grid;
        place-items: center;
        min-height: 420px;
        text-align: center;
        color: #64748b;
      }

      .proposal-empty-preview {
        gap: 8px;
      }

      .proposal-image-preview {
        display: block;
        max-width: 100%;
        max-height: 72vh;
        margin: 0 auto;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
      }

      .proposal-review-comments {
        display: flex;
        flex-direction: column;
        overflow: hidden;
      }

      .comments-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
      }

      .comments-header h2 {
        margin: 0;
        color: #0f172a;
        font-size: 17px;
      }

      .comments-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
      }

      .comments-list {
        flex: 1;
        overflow: auto;
        padding: 14px;
        display: grid;
        align-content: start;
        gap: 10px;
      }

      .comment-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        background: #ffffff;
      }

      .comment-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 8px;
      }

      .comment-meta strong {
        color: #0f172a;
      }

      .comment-body {
        color: #1f2937;
        font-size: 13px;
        line-height: 1.55;
      }

      .comment-body small {
        display: inline-flex;
        margin-bottom: 6px;
        color: #0f766e;
        font-weight: 800;
      }

      .comment-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 18px;
        color: #64748b;
        text-align: center;
      }

      .comment-form {
        padding: 16px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
      }

      .comment-form textarea {
        min-height: 150px;
        resize: vertical;
      }

      .comment-form .btn-sm {
        width: 100%;
      }

      @media (max-width: 1080px) {
        .proposal-review-shell {
          grid-template-columns: 1fr;
        }

        .proposal-review-header {
          flex-direction: column;
        }

        .proposal-review-actions {
          justify-content: flex-start;
          min-width: 0;
          flex-wrap: wrap;
        }
      }
    </style>
  <?php $__env->stopPush(); ?>

  <?php if($isPdf): ?>
    <script>
      const pdfContainer = document.getElementById('pdf-container');
      const pdfJsUrl = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js';
      const pdfWorkerUrl = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
      const existingComments = <?php echo json_encode($comments->toArray(), 15, 512) ?>;
      let pdfDoc = null;
      let scale = 1.25;

      function loadScript(src) {
        return new Promise((resolve, reject) => {
          const script = document.createElement('script');
          script.src = src;
          script.onload = resolve;
          script.onerror = reject;
          document.head.appendChild(script);
        });
      }

      async function ensurePdfJs() {
        if (window.pdfjsLib) return;
        await loadScript(pdfJsUrl);
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;
      }

      function addMarker(overlay, location, color, title) {
        const marker = document.createElement('div');
        marker.style.position = 'absolute';
        marker.style.left = (location.x * 100) + '%';
        marker.style.top = (location.y * 100) + '%';
        marker.style.transform = 'translate(-50%,-50%)';
        marker.style.width = '12px';
        marker.style.height = '12px';
        marker.style.borderRadius = '50%';
        marker.style.background = color;
        marker.style.boxShadow = '0 0 0 4px rgba(15,23,42,0.12)';
        marker.title = title || '';
        overlay.appendChild(marker);
      }

      async function loadPdf(url) {
        await ensurePdfJs();
        pdfContainer.innerHTML = '';
        const loadingTask = window.pdfjsLib.getDocument(url);
        pdfDoc = await loadingTask.promise;

        for (let i = 1; i <= pdfDoc.numPages; i++) {
          const page = await pdfDoc.getPage(i);
          const viewport = page.getViewport({ scale });
          const canvas = document.createElement('canvas');
          canvas.id = 'page-' + i;
          canvas.width = viewport.width;
          canvas.height = viewport.height;
          canvas.style.display = 'block';
          canvas.style.width = '100%';
          canvas.style.height = 'auto';
          canvas.style.background = '#ffffff';

          const wrapper = document.createElement('div');
          wrapper.style.position = 'relative';
          wrapper.style.maxWidth = '980px';
          wrapper.style.margin = '0 auto 18px';
          wrapper.style.background = '#ffffff';
          wrapper.style.boxShadow = '0 12px 28px rgba(15,23,42,0.12)';
          wrapper.appendChild(canvas);

          const overlay = document.createElement('div');
          overlay.className = 'page-overlay';
          overlay.style.position = 'absolute';
          overlay.style.inset = '0';
          overlay.style.pointerEvents = 'auto';
          wrapper.appendChild(overlay);
          pdfContainer.appendChild(wrapper);

          await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

          overlay.addEventListener('click', function (event) {
            const rect = canvas.getBoundingClientRect();
            const location = {
              x: parseFloat(((event.clientX - rect.left) / rect.width).toFixed(4)),
              y: parseFloat(((event.clientY - rect.top) / rect.height).toFixed(4)),
            };
            addMarker(overlay, location, '#ef4444', 'New feedback marker');

            const pageInput = document.querySelector('#comment-form [name="page"]');
            const locationInput = document.querySelector('#comment-form [name="location"]');
            if (pageInput) pageInput.value = i;
            if (locationInput) locationInput.value = JSON.stringify(location);
          });
        }

        existingComments.forEach((comment) => {
          try {
            const location = comment.location ? JSON.parse(comment.location) : null;
            if (!location || !comment.page) return;
            const overlay = document.querySelector('#page-' + comment.page)?.parentElement?.querySelector('.page-overlay');
            if (overlay) addMarker(overlay, location, '#0f766e', (comment.comment_text || '').slice(0, 120));
          } catch (error) {
            console.error(error);
          }
        });
      }

      loadPdf(<?php echo json_encode($selectedUrl, 15, 512) ?>).catch((error) => {
        console.error(error);
        pdfContainer.innerHTML = '<div class="proposal-empty-preview"><strong>Unable to load PDF preview.</strong><span>Use Download to open the document.</span></div>';
      });
    </script>
  <?php endif; ?>

  <script>
    document.getElementById('comment-form').addEventListener('submit', async function (event) {
      event.preventDefault();
      const form = event.target;
      const response = await fetch('<?php echo e(route('admin.proposals.comments', $proposal->id)); ?>', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: new FormData(form),
      });

      const json = await response.json();
      if (!json.success) {
        alert('Could not save feedback.');
        return;
      }

      const comment = json.comment;
      const container = document.getElementById('comments-list');
      const item = document.createElement('div');
      item.className = 'comment-item';
      const safeText = (comment.comment_text || '').replace(/[&<>"']/g, function (char) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char];
      }).replace(/\n/g, '<br>');
      item.innerHTML = `
        <div class="comment-meta"><strong>${comment.commenter_role || 'You'}</strong><small>just now</small></div>
        <div class="comment-body">${comment.page ? '<small>Page ' + comment.page + '</small>' : ''}<div>${safeText}</div></div>
      `;
      const empty = container.querySelector('.comment-empty');
      if (empty) empty.remove();
      container.prepend(item);
      form.reset();
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/admin/proposals/pdf-view.blade.php ENDPATH**/ ?>