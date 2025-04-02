<div>
    <form action="{{$formUrl}}" method="post">
        @csrf
        @if($method == 'put')
        @method('PUT')
        @endif
        <!-- compliance owner -->
        <div class="mb-3">
            <label for="compliance_owner" class="form-label">Owner</label>
            <input type="text" class="form-control" name="compliance_owner" id="compliance_owner" placeholder="name@example.com" value="{{$formData['compliance_owner']}}">
        </div>

        <!-- role -->
        <div class="mb-3">
            <label for="role">Select Role</label>
            <select id="role" name="role" class="form-control">
                <option value="">Choose a Role</option>
                <option value="compliance_owner" {{ $formData['role'] === 'compliance_owner' ? 'selected' : '' }}>Compliance Owner</option>
                <option value="FAP" {{ $formData['role'] === 'FAP' ? 'selected' : '' }}>FAP</option>
            </select>
        </div>

        <!-- line manager -->
        <div class="mb-3">
            <label for="line_manager" class="form-label">Line Manager</label>
            <input type="text" class="form-control" name="line_manager" id="line_manager" placeholder="name@example.com" value="{{$formData['line_manager']}}">
        </div>
        <!-- emt -->
        <div class="mb-3">
            <label for="emt" class="form-label">EMT</label>
            <input type="text" class="form-control" name="emt" id="emt" placeholder="name@example.com" value="{{$formData['emt']}}">
        </div>
        <!-- cxo -->
        <div class="mb-3">
            <label for="cxo" class="form-label">CXO</label>
            <input type="text" class="form-control" name="cxo" id="cxo" placeholder="name@example.com" value="{{$formData['cxo']}}">
        </div>
        <!-- ceo -->
        <div class="mb-3">
            <label for="ceo" class="form-label">CEO</label>
            <input type="text" class="form-control" name="ceo" id="ceo" placeholder="name@example.com" value="{{$formData['ceo']}}">
        </div>

        <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
</form>

</div>
