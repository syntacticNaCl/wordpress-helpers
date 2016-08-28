<!-- ko if: 'table' == selectionType() -->

<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>User</th>
			<th>Toggle</th>
		</tr>
	</thead>
	<tbody>
    <!-- ko foreach: { data: filteredUsers, as: 'user' } -->
		<tr>
			<td data-bind="text: user.email"></td>
			<td>
                @include('admin.meta-boxes.post-users-pivot.select-users.toggle-button')
            </td>
		</tr>
    <!-- /ko -->
	</tbody>
</table>

<!-- /ko -->