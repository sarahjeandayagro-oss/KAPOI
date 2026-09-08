@php
  $showActions = $showActions ?? false;
  $emptyMessage = $emptyMessage ?? 'No borrow requests yet.';
  $statusColumn = $statusColumn ?? false;
  $showArchiveAction = $showArchiveAction ?? false;
  $showRejectionReason = $showRejectionReason ?? false;
@endphp
<table>
  <thead>
    <tr>
      <th>User</th>
      <th>Email</th>
      <th>Role</th>
      <th>Book</th>
      <th>Accession</th>
      <th>Requested At</th>
      @if($statusColumn)
        <th>Status</th>
        @if($showRejectionReason)
          <th>Rejection Reason</th>
        @endif
        @if($showArchiveAction)
          <th>Action</th>
        @endif
      @elseif($showActions)
        <th>Action</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @forelse($requests as $request)
      <tr>
        <td>{{ $request->user_name ?? 'Unknown' }}<br><span class="text-muted">{{ $request->user_id }}</span></td>
        <td>{{ $request->user_email ?? 'n/a' }}</td>
        <td>{{ ucfirst($request->user_role ?? 'student') }}</td>
        <td>{{ $request->title ?? 'Unknown book' }}<br><span class="text-muted">{{ $request->author }}</span></td>
        <td>{{ $request->accession_number ?? 'N/A' }}</td>
        <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
        @if($statusColumn)
          <td>
            <span class="status-chip {{ in_array(strtolower($request->status), ['approved', 'borrowed']) ? 'approved' : (strtolower($request->status) === 'rejected' ? 'danger' : 'pending') }}">
              {{ ucfirst($request->status ?? 'Approved') }}
            </span>
          </td>
          @if($showRejectionReason)
            <td>{{ $request->rejection_reason ?? 'No reason provided.' }}</td>
          @endif
          @if($showArchiveAction)
            <td style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
              @if(empty($request->is_dummy))
                <form method="POST" action="{{ route('admin.borrow-requests.archive', $request->id) }}">
                  @csrf
                  <button class="btn-sm" type="submit">Archive</button>
                </form>
              @else
                <span class="status-chip pending">Sample request</span>
              @endif
            </td>
          @endif
        @elseif($showActions)
          <td style="display:flex; gap:8px; flex-wrap:wrap; align-items:flex-start;">
            @if(empty($request->is_dummy))
              <form method="POST" action="{{ route('admin.borrow-requests.approve', $request->id) }}">
                @csrf
                <input type="hidden" name="borrowing_transaction_id" value="{{ $request->id }}">
                <button class="btn-sm" type="submit">Approve</button>
              </form>
              <form method="POST" action="{{ route('admin.borrow-requests.archive', $request->id) }}">
                @csrf
                <button class="btn-sm" type="submit">Archive</button>
              </form>
              <form method="POST" action="{{ route('admin.borrow-requests.reject', $request->id) }}" style="display:flex; flex-direction:column; gap:6px; max-width:240px;">
                @csrf
                <input type="hidden" name="borrowing_transaction_id" value="{{ $request->id }}">
                <textarea name="reason" placeholder="Reject reason" required style="width:220px; min-height:60px; padding:8px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;"></textarea>
                <button class="btn-sm danger" type="submit">Reject</button>
              </form>
            @else
              <span class="status-chip pending">Sample request</span>
            @endif
          </td>
        @endif
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;">{{ $emptyMessage }}</td></tr>
    @endforelse
  </tbody>
</table>
