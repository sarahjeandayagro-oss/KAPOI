@extends('layouts.app')

@section('title', 'Panabo City Library - Researcher Portal')

@php
  $proposals = $proposals ?? collect();
  $proposalCount = $proposalCount ?? $proposals->count();

  $sidebar = view('components.sidebar', [
    'brand' => 'Panabo City Library',
    'subtitle' => 'Researcher Portal',
    'avatar' => auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'R',
    'userName' => auth()->user()?->name ?? 'Researcher',
    'userRole' => 'Researcher',
    'activePanel' => session('active_panel', 'dashboard'),
  ]);

  $topbar = view('components.topbar', [
    'title' => 'Researcher Dashboard',
    'subtitle' => 'Welcome back, ' . (auth()->user()?->name ?? 'Researcher'),
  ]);
@endphp

@section('content')
  {{-- DASHBOARD PANEL --}}
  <section id="panel-dashboard" class="panel active">
    <div class="grid-3">
      @include('components.stat-card', ['label' => 'Proposals Submitted', 'value' => $proposalCount, 'change' => 'Active submissions'])
      @include('components.stat-card', ['label' => 'Approved Projects', 'value' => $proposals->where('status', 'Approved')->count(), 'change' => 'For tracking'])
      @include('components.stat-card', ['label' => 'Research Funds', 'value' => 'PHP ' . number_format(100000), 'change' => 'Allocated grant'])
    </div>

    @component('components.card', ['title' => 'Proposal Overview', 'subtitle' => 'Research work status'])
      <table>
        <thead><tr><th>Title</th><th>Status</th><th>Deadline</th></tr></thead>
        <tbody>
          @forelse($proposals->take(5) as $proposal)
            <tr>
              <td>{{ $proposal->title ?? 'Untitled Proposal' }}</td>
              <td><span class="status-chip {{ ($proposal->status ?? 'Pending') === 'Approved' ? 'approved' : 'pending' }}">{{ $proposal->status ?? 'Pending' }}</span></td>
              <td>{{ isset($proposal->deadline) ? \Carbon\Carbon::parse($proposal->deadline)->format('M d, Y') : 'N/A' }}</td>
            </tr>
          @empty
            <tr><td colspan="3">No proposals yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent
  </section>

  {{-- PANEL 2: PROPOSAL SUBMISSION WORKSPACE (FORM) --}}
  <section id="panel-submit" class="panel">
    <div class="grid-1">
      <div class="template-box" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:18px; box-shadow:0 12px 30px rgba(15,23,42,0.08); padding:22px 24px; margin-bottom:20px;">
        <div style="font-weight: 700; font-size: 13px; margin-bottom: 12px; color: #0f172a; letter-spacing: 0.02em;">Annex B — RESEARCH PROPOSAL TEMPLATE SPECIFICATIONS</div>
        <div style="font-size: 12px; color: #334155; line-height: 1.7;">
          <strong>Font Style:</strong> Arial  |  <strong>Font Size:</strong> 12  |  <strong>Spacing:</strong> 1.5 pt  |  <strong>No. of Pages:</strong> N/A
        </div>
        <div style="margin-top: 18px; font-size: 12px; color: #334155; line-height: 1.8;">
          <div style="font-weight: 700; margin-bottom: 8px;">CHAPTER I</div>
          <div style="margin-left: 12px;">
            • TITLE OF THE STUDY<br />
            • BACKGROUND OF THE STUDY<br />
            • RESEARCH HYPOTHESES (OPTIONAL)<br />
            • STATEMENT OF THE PROBLEM<br />
            • OBJECTIVES<br />
            • SCOPE AND DELIMITATIONS<br />
            • THEORETICAL FRAMEWORK<br />
            • CONCEPTUAL FRAMEWORK<br />
            • DEFINITION OF TERMS
          </div>

          <div style="font-weight: 700; margin: 16px 0 8px;">CHAPTER II</div>
          <div style="margin-left: 12px;">
            • REVIEW OF RELATED LITERATURE<br />
            • REFERENCES (APA Format)
          </div>

          <div style="font-weight: 700; margin: 16px 0 8px;">CHAPTER III</div>
          <div style="margin-left: 12px;">
            • RESEARCH METHODOLOGY/DESIGN<br />
            • QUESTIONNAIRE/INSTRUMENTS USED FOR THE RESEARCH
          </div>
        </div>
      </div>

      <div class="researcher-info-box" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:18px; box-shadow:0 16px 40px rgba(15,23,42,0.08); padding:22px 24px; margin-top:20px;">
        <div style="font-weight: 700; color: #1e293b; margin-bottom: 12px; font-size: 14px; letter-spacing: 0.03em;">C. THE RESEARCHER</div>
        <div style="overflow-x:auto; margin-bottom: 20px; border-radius: 14px;">
          <table style="width:100%; border-collapse:collapse; min-width:700px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow: inset 0 0 0 1px rgba(226,232,240,0.8);">
            <thead>
              <tr style="background:#eef2ff; color:#1e3a8a; text-align:left; border-bottom:2px solid #c7d2fe;">
                <th style="padding:12px 14px; width:8%;">No.</th>
                <th style="padding:12px 14px;">Requirement</th>
              </tr>
            </thead>
            <tbody>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">1</td>
                <td style="padding:12px 14px;">The Researcher shall be given four (4) to six (6) months to implement his/her proposal and arrive at a result, analysis, conclusion, and recommendation.</td>
              </tr>
              <tr style="background:#f8fafc; border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">2</td>
                <td style="padding:12px 14px;">He/she shall receive the grant in a staggered basis outlined below.</td>
              </tr>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">3</td>
                <td style="padding:12px 14px;">He/she shall submit two (2) hardbound copies of his/her final and approved research to the Research Grant Secretariat at the City Library.</td>
              </tr>
              <tr>
                <td style="padding:12px 14px; vertical-align:top;">4</td>
                <td style="padding:12px 14px;">If the researcher fails to perform his/her obligations, the LGU may terminate the contract and the researcher shall pay the LGU in full according to the amount duly granted.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div style="overflow-x:auto; margin-bottom: 20px; border-radius: 14px;">
          <table class="staggered-table" style="width:100%; border-collapse:collapse; min-width:700px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow: inset 0 0 0 1px rgba(226,232,240,0.8);">
            <thead>
              <tr style="background:#eef2ff; color:#1e3a8a; text-align:left; border-bottom:2px solid #c7d2fe;">
                <th style="width: 25%; padding:12px 14px;">DURATION</th>
                <th style="width: 25%; padding:12px 14px;">AMOUNT</th>
                <th style="width: 50%; padding:12px 14px;">EXPECTED OUTPUT/DELIVERABLE</th>
              </tr>
            </thead>
            <tbody>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px;"><b>First month</b></td>
                <td style="padding:12px 14px;"><b>₱30,000.00</b><br><span style="font-size:11px; color:#6b7280;">(30% of the total grant)</span></td>
                <td style="padding:12px 14px;">Certification of Approved Research by the RGEC</td>
              </tr>
              <tr style="background:#f8fafc; border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px;"><b>After 2-3 months</b></td>
                <td style="padding:12px 14px;"><b>₱30,000.00</b><br><span style="font-size:11px; color:#6b7280;">(30% of the total grant)</span></td>
                <td style="padding:12px 14px;">Result of the study. If quantitative study, a statistical analysis certified by a statistician.</td>
              </tr>
              <tr>
                <td style="padding:12px 14px;"><b>Next 2-3 months</b></td>
                <td style="padding:12px 14px;"><b>₱40,000.00</b><br><span style="font-size:11px; color:#6b7280;">(40% of the total grant)</span></td>
                <td style="padding:12px 14px;">Presentation of the research interpretation, analysis, and recommendation.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div style="font-weight: 700; color: #1e293b; margin-top: 16px; margin-bottom: 12px;">D. THE LOCAL GOVERNMENT UNIT</div>
        <div style="overflow-x:auto; border-radius: 14px;">
          <table style="width:100%; border-collapse:collapse; min-width:700px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow: inset 0 0 0 1px rgba(226,232,240,0.8);">
            <thead>
              <tr style="background:#eef2ff; color:#1e3a8a; text-align:left; border-bottom:2px solid #c7d2fe;">
                <th style="padding:12px 14px; width:8%;">No.</th>
                <th style="padding:12px 14px;">Responsibility</th>
              </tr>
            </thead>
            <tbody>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">1</td>
                <td style="padding:12px 14px;">Set guidelines, mechanics, and criteria for the submission of the research papers.</td>
              </tr>
              <tr style="background:#f8fafc; border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">2</td>
                <td style="padding:12px 14px;">Invite institutions of higher education for applications for research grants from students of academic staff.</td>
              </tr>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">3</td>
                <td style="padding:12px 14px;">Review and evaluate research papers in accordance with existing guidelines and criteria.</td>
              </tr>
              <tr style="background:#f8fafc; border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px 14px; vertical-align:top;">4</td>
                <td style="padding:12px 14px;">Endorse applicants to the Local Chief Executive for recommendation for cash assistance.</td>
              </tr>
              <tr>
                <td style="padding:12px 14px; vertical-align:top;">5</td>
                <td style="padding:12px 14px;">Administer grants and store copies of the research papers at the City Library as properties of the City Government.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      @component('components.card', ['title' => 'Proposal Entry Form Data'])
        <form id="proposalForm" action="{{ route('proposal.submit') }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmission(event)">
          @csrf
          <div class="form-group">
            <label>Proposed Research Project Title</label>
            <input type="text" name="title" placeholder="Enter your full targeted system proposal title..." required/>
          </div>
          <div class="form-group">
            <label>Research Proposal PDF</label>
            <input type="file" name="app_form" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required style="width:100%;" />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Required Funding Budget Allocation (₱)</label>
              <!-- use text input for formatted display; value will be unformatted before submit -->
              <input type="text" name="budget" placeholder="e.g., 100,000.00" inputmode="decimal" required pattern="^[0-9,]*\.?[0-9]{0,2}$" />
            </div>
            <div class="form-group">
              <label>Target Submission Review Deadline</label>
              <input type="date" name="deadline" required/>
            </div>
          </div>

          <div class="form-group">
            <label>General Specifications Summary Notes</label>
            <textarea name="requirements" placeholder="Write a brief introduction regarding your system features or infrastructural goals..."></textarea>
          </div>

          <div style="margin: 20px 0 10px 0; font-weight: 600; font-size: 13px; color: var(--purple); text-transform: uppercase; border-bottom: 1px solid #dcdfe6; padding-bottom: 6px;">
            Mandatory Institutional Documents Checklist (Supports both Document files & snapshots/pictures)
          </div>

          <div class="form-row">
            <div class="form-group"><label>a. Application Form</label><input type="file" name="application_form" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required style="width:100%;"/></div>
            <div class="form-group"><label>b. Curriculum Vitae</label><input type="file" name="cv_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required style="width:100%;"/></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>c. Budgetary Requirement (Annex A)</label><input type="file" name="budget_sheet" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required style="width:100%;"/></div>
            <div class="form-group"><label>d. Project Proposal Supporting Documents</label><input type="file" name="proposal_supporting_docs" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required style="width:100%;"/></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>e. Certification from Research Advisor/Institution</label><input type="file" name="advisory_cert" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required style="width:100%;"/></div>
            <div class="form-group"><label>f. Valid ID or Barangay Certificate</label><input type="file" name="id_photocopy" accept=".pdf,.jpg,.jpeg,.png" required style="width:100%;"/></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>g. Company/Agency/School ID <span>(Optional)</span></label><input type="file" name="institution_id" accept=".pdf,.jpg,.jpeg,.png" style="width:100%;"/></div>
            <div class="form-group"><label>h. Certificate of City Mayor/Study Leave <span>(Optional)</span></label><input type="file" name="lgu_cert" accept=".pdf,.jpg,.jpeg,.png" style="width:100%;"/></div>
          </div>

          <div style="display: flex; justify-content: flex-end; margin-top: 25px; border-top: 1px solid #dcdfe6; padding-top: 15px;">
            <button type="submit" id="submitProposalBtn" class="btn primary">Transmit Proposal Matrix Bundle</button>
          </div>
        </form>
      @endcomponent
    </div>
  </section>

  {{-- MY PROPOSALS PANEL --}}
  <section id="panel-proposals" class="panel">
    @component('components.card', ['title' => 'My Proposals'])
      <table>
        <thead><tr><th>Title</th><th>Status</th><th>Budget</th></tr></thead>
        <tbody>
          @forelse($proposals as $proposal)
            <tr>
              <td>{{ $proposal->title ?? 'Untitled Proposal' }}</td>
              <td><span class="status-chip {{ ($proposal->status ?? 'Pending') === 'Approved' ? 'approved' : 'pending' }}">{{ $proposal->status ?? 'Pending' }}</span></td>
              <td>PHP {{ number_format($proposal->budget ?? 0, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="3">No proposals yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent
  </section>

  <div class="modal-overlay" id="staggeredModalOverlay">
    <div class="modal-card">
      <div class="modal-header">Research Project Submission Terms of Reference</div>
      <div class="modal-body">
        <div style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 12px; border-bottom: 1px solid #dcdfe6; padding-bottom: 6px;">TERMS OF REFERENCES AGREEMENT DOCUMENT</div>
        <p style="margin-bottom: 12px; text-align: justify;">Please confirm that you want to submit this research proposal bundle for review. After submission, the library staff will evaluate the uploaded documents and proposal details.</p>
        <div style="background: #fff8e1; border: 1px solid #ffe082; padding: 14px; border-radius: 8px;">
          <label class="checkbox-row" style="color: #b38f2d; font-weight: 600;">
            <input type="checkbox" id="modalAgreementCheckbox" onchange="toggleModalSubmitActivation()">
            <span>I confirm that the proposal details and uploaded attachments are correct and ready for review.</span>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="closeModalWindow()">Cancel</button>
        <button type="button" class="btn primary" id="modalSubmitBtn" onclick="confirmFinalFormSubmission()" disabled>Accept Terms & Submit</button>
      </div>
    </div>
  </div>

  <section id="panel-notifications" class="panel">
    @component('components.card', ['title' => 'Notifications', 'subtitle' => 'Messages sent to your account'])
      @if(isset($notifications) && $notifications->count())
        <table>
          <thead><tr><th>Message</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
            @foreach($notifications as $notification)
              <tr>
                <td>{{ $notification->data['message'] ?? $notification->data['title'] ?? 'Notification' }}</td>
                <td><span class="status-chip {{ $notification->read_at ? 'returned' : 'pending' }}">{{ $notification->read_at ? 'Read' : 'Unread' }}</span></td>
                <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y h:i A') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div style="padding:24px; text-align:center; color:#6b7280;">No notifications yet.</div>
      @endif
    @endcomponent
  </section>

  @push('head')
    <style>
      .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(31, 41, 55, 0.35);
        backdrop-filter: blur(2px);
        z-index: 200;
        align-items: center;
        justify-content: center;
      }
      .modal-overlay.open {
        display: flex !important;
      }

      .modal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        width: 620px;
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        display: flex;
        flex-direction: column;
      }
      .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 16px;
        font-weight: 700;
        color: #111827;
      }
      .modal-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        color: #334155;
      }
      .modal-footer {
        padding: 16px 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        border-top: 1px solid #e2e8f0;
      }
      .modal-footer .btn {
        min-width: 140px;
      }
      .checkbox-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
      }
      .checkbox-row input[type="checkbox"] {
        margin-top: 4px;
      }
    </style>
  @endpush

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('staggeredModalOverlay');
        if (overlay) {
          overlay.addEventListener('click', function(event) {
            if (event.target === overlay) {
              overlay.classList.remove('open');
            }
          });
        }
      });

      function handleFormSubmission(event) {
        event.preventDefault();
        const overlay = document.getElementById('staggeredModalOverlay');
        if (!overlay) return;

        overlay.classList.add('open');
        const checkbox = document.getElementById('modalAgreementCheckbox');
        const submitButton = document.getElementById('modalSubmitBtn');
        if (checkbox) checkbox.checked = false;
        if (submitButton) submitButton.disabled = true;
      }

      function closeModalWindow() {
        const overlay = document.getElementById('staggeredModalOverlay');
        if (overlay) overlay.classList.remove('open');
      }

      function toggleModalSubmitActivation() {
        const checkbox = document.getElementById('modalAgreementCheckbox');
        const submitButton = document.getElementById('modalSubmitBtn');
        if (submitButton) submitButton.disabled = !checkbox?.checked;
      }

      function confirmFinalFormSubmission() {
        const form = document.getElementById('proposalForm');
        if (!form) return;

        // Before final submit, unformat the budget field so server receives plain numeric value
        const budgetInput = form.querySelector('[name="budget"]');
        if (budgetInput) {
          // remove commas and whitespace
          const raw = (budgetInput.value || '').toString().replace(/,/g, '').trim();
          budgetInput.value = raw;
        }
        const overlay = document.getElementById('staggeredModalOverlay');
        if (overlay) overlay.classList.remove('open');
        form.removeAttribute('onsubmit');
        form.submit();
      }
    </script>
  @endpush

  @push('scripts')
    <script>
      // Fallback script to ensure modal and submission functions are available even if earlier inline scripts fail.
      (function () {
        function enableModalHandlers() {
          const overlay = document.getElementById('staggeredModalOverlay');
          if (overlay && !overlay._hasModalHandler) {
            overlay.addEventListener('click', function (e) { if (e.target === overlay) overlay.classList.remove('open'); });
            overlay._hasModalHandler = true;
          }

          const chk = document.getElementById('modalAgreementCheckbox');
          const submitBtn = document.getElementById('modalSubmitBtn');
          if (submitBtn) submitBtn.disabled = true;
          if (chk && submitBtn) chk.addEventListener('change', function () { submitBtn.disabled = !this.checked; });

          // Robust budget formatter/helper attached here to enforce numeric-only input and sanitize pasted values
          function formatBudgetValue(raw, forceDecimals) {
            let s = (raw || '').toString().replace(/[^0-9.]/g, '');
            const parts = s.split('.');
            if (parts.length > 1) s = parts.shift() + '.' + parts.join('');
            const [intPart, decPart] = s.split('.');
            let intClean = intPart ? intPart.replace(/^0+(?=\d)/, '') : '';
            intClean = intClean.slice(0, 9); // max 9 integer digits (supports up to 999,999,999)
            const intFormatted = intClean ? Number(intClean).toLocaleString('en-US') : (intClean === '0' ? '0' : '');
            if (typeof decPart === 'undefined' || decPart === '') {
              return forceDecimals ? (intFormatted || '0') + '.00' : intFormatted;
            }
            return intFormatted + '.' + decPart.slice(0, 2);
          }

          const budgetInput = document.querySelector('input[name="budget"]');
          if (budgetInput) {
            // input handler: sanitize and format
            budgetInput.addEventListener('input', function () {
              const cleaned = (this.value || '').toString().replace(/[^0-9.]/g, '');
              // collapse multiple dots keeping first
              const parts = cleaned.split('.');
              const normalized = parts.length > 1 ? parts.shift() + '.' + parts.join('') : parts[0];
              // limit decimals to 2
              const [intPart, decPart] = normalized.split('.');
              const valueToFormat = typeof decPart === 'undefined' ? intPart : (intPart + '.' + decPart.slice(0,2));
              this.value = formatBudgetValue(valueToFormat);
            });

            // paste handler: sanitize pasted content
            budgetInput.addEventListener('paste', function (e) {
              e.preventDefault();
              const text = (e.clipboardData || window.clipboardData).getData('text') || '';
              const cleaned = text.replace(/[^0-9.]/g, '');
              this.value = formatBudgetValue(cleaned);
              try { this.setSelectionRange(this.value.length, this.value.length); } catch (ex) {}
            });

            // keydown filter: block comma, dash, and non-digit keys (commas come only from auto-formatting)
            budgetInput.addEventListener('keydown', function (e) {
              const allowed = ['Backspace','ArrowLeft','ArrowRight','Delete','Tab','Home','End'];
              if (allowed.indexOf(e.key) !== -1) return;
              // Commas and dashes are never typed — they come only from auto-formatting
              if (e.key === ',' || e.key === '-') { e.preventDefault(); return; }
              if (e.key === '.') { if (this.value.indexOf('.') !== -1) { e.preventDefault(); } return; }
              if (!/^[0-9]$/.test(e.key)) { e.preventDefault(); }
            });

            // blur: ensure formatting with 2 decimal places
            budgetInput.addEventListener('blur', function () {
              if (this.value.trim() !== '') this.value = formatBudgetValue(this.value, true);
            });
          }

          // expose functions globally (idempotent)
          window.handleFormSubmission = window.handleFormSubmission || function (event) {
            if (event && event.preventDefault) event.preventDefault();
            const ov = document.getElementById('staggeredModalOverlay'); if (!ov) return; ov.classList.add('open');
            const cb = document.getElementById('modalAgreementCheckbox'); const sb = document.getElementById('modalSubmitBtn'); if (cb) cb.checked = false; if (sb) sb.disabled = true;
          };

          window.toggleModalSubmitActivation = window.toggleModalSubmitActivation || function () {
            try { var c = document.getElementById('modalAgreementCheckbox'); var s = document.getElementById('modalSubmitBtn'); if (!s) return; s.disabled = !(c && c.checked); } catch (e) { console.error(e); }
          };

          window.confirmFinalFormSubmission = window.confirmFinalFormSubmission || function () {
            try {
              var form = document.getElementById('proposalForm'); if (!form) return;
              var budgetInput = form.querySelector('[name="budget"]'); if (budgetInput) budgetInput.value = (budgetInput.value||'').toString().replace(/,/g,'').trim();
              var ov = document.getElementById('staggeredModalOverlay'); if (ov) ov.classList.remove('open'); form.removeAttribute('onsubmit'); form.submit();
            } catch (e) { console.error(e); alert('Unable to submit form. Please check the form fields and try again.'); }
          };
        }

        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', enableModalHandlers); else enableModalHandlers();
      })();
    </script>
  @endpush

@endsection
