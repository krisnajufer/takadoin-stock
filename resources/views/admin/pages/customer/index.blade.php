<x-admin.layouts.list title="Customer" :search_input="['ID', 'Name', 'Phone', 'Gender']" :table_columns="['ID', 'Name', 'Phone', 'Gender', 'Status']">
    <tr>
        <td>
            <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input check-data" type="checkbox">
            </div>
        </td>
        <td><a href="javascript:void(0)" class="text-primary-600">#526534</a></td>
        <td>
            <div class="d-flex align-items-center">
                <img src="assets/images/user-list/user-list1.png" alt="" class="flex-shrink-0 me-12 radius-8">
                <h6 class="text-md mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
            </div>
        </td>
        <td>25 Jan 2024</td>
        <td>$200.00</td>
        <td> <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Paid</span>
        </td>
    </tr>
    <tr>
        <td>
            <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input check-data" type="checkbox">
            </div>
        </td>
        <td><a href="javascript:void(0)" class="text-primary-600">#526534</a></td>
        <td>
            <div class="d-flex align-items-center">
                <img src="assets/images/user-list/user-list1.png" alt="" class="flex-shrink-0 me-12 radius-8">
                <h6 class="text-md mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
            </div>
        </td>
        <td>25 Jan 2024</td>
        <td>$200.00</td>
        <td> <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Paid</span>
        </td>
    </tr>
</x-admin.layouts.list>
