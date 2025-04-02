<div>
    <form action="{{$formUrl}}" method="post">
        @csrf
        @if($method == 'put')
        @method('PUT')
        @endif

        <div class="mb-3">
            <label for="compliance_owner" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="name" value="{{$formData['name']?? ''}}">
        </div>

        <div class="mb-3">
            <label for="line_manager" class="form-label">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="name@example.com" value="{{$formData['email']?? ''}}">
        </div>

        <div class="mb-3">
            <label for="emt" class="form-label">Mobile</label>
            <input type="text" class="form-control" name="mobile" id="mobile" placeholder="01711..." value="{{$formData['mobile']?? ''}}">
        </div>

        <div class="mb-3">
            <label for="cxo" class="form-label">Designation</label>
            <input type="text" class="form-control" name="designation" id="designation" placeholder="designation" value="{{$formData['designation']?? ''}}">
        </div>

        <div class="mb-3">
            <label for="role">Select Status</label>
            <select id="status" name="status" class="form-control">
                <option value="">Choose a Role</option>
                <option value="active" {{ $formData['status']?? '' === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $formData['status']?? '' === 'inactive' ? 'selected' : '' }}>InActive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
</form>

</div>
