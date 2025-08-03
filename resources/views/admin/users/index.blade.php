@extends('admin.layouts.app')

@section('content')
    <style>
        .users-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .users-header {
            background: #991b1b;
            color: white;
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .users-header h1 {
            font-size: 1.875rem;
            font-weight: 600;
            margin: 0;
        }

        .table-container {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .users-table th {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }

        .users-table td {
            border: 1px solid #e5e7eb;
            padding: 12px 8px;
            text-align: left;
            vertical-align: top;
        }

        .users-table tbody tr:hover {
            background: #f9fafb;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .role-admin {
            background: #fee2e2;
            color: #991b1b;
        }

        .role-user {
            background: #dbeafe;
            color: #1e40af;
        }

        .gender-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 0.75rem;
            text-transform: capitalize;
        }

        .gender-male {
            background: #dbeafe;
            color: #1e40af;
        }

        .gender-female {
            background: #fce7f3;
            color: #be185d;
        }

        .gender-other {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .status-verified {
            color: #059669;
            font-weight: 500;
        }

        .status-unverified {
            color: #dc2626;
            font-weight: 500;
        }

        .updated-by-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.75rem;
            background: #e0f2fe;
            color: #0277bd;
            font-weight: 500;
        }

        .self-updated {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .btn-primary,
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #059669;
            color: white;
        }

        .btn-primary:hover {
            background: #047857;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .role-selector {
            width: 70%;
            padding: 6px 2px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.75rem;
            background: white;
        }

        .role-selector:focus {
            outline: none;
            border-color: #991b1b;
            box-shadow: 0 0 0 2px rgba(153, 27, 27, 0.1);
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            justify-content: center;
            align-items: center;
        }

        .btn-edit,
        .btn-delete {
            width: 80%;
            height: 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #fbbf24;
            color: #92400e;
        }

        .btn-edit:hover {
            background: #f59e0b;
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .role-updating {
            opacity: 0.6;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .users-header>div {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-edit,
            .btn-delete {
                width: 100%;
                height: 28px;
            }
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .users-header h1 {
                font-size: 1.5rem;
            }

            .users-table {
                font-size: 0.75rem;
            }

            .users-table th,
            .users-table td {
                padding: 8px 4px;
            }
        }
    </style>

    <div class="users-container">
        <div class="users-header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <h1>All Users ({{ count($users) }})</h1>
            </div>
        </div>

        <div class="table-container">
            @if(count($users) > 0)
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Date of Birth</th>
                            <th>Role</th>
                            <th>Email Verified</th>
                            <th>Updated By</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td style="text-transform: lowercase">{{ $user->email }}</td>
                                <td>{{ $user->phoneno ?? 'N/A' }}</td>
                                <td>
                                    @if($user->gender)
                                        <span class="gender-badge gender-{{ $user->gender }}">
                                            {{ ucfirst($user->gender) }}
                                        </span>
                                    @else
                                        <span class="gender-badge gender-other">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $user->address ?? 'N/A' }}</td>
                                <td>
                                    @if($user->date_of_birth)
                                        {{ \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') }}
                                        <br>
                                        <small style="color: #6b7280;">
                                            ({{ \Carbon\Carbon::parse($user->date_of_birth)->age }} years old)
                                        </small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <select class="role-selector" data-user-id="{{ $user->id }}" onchange="updateUserRole(this)">
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </option>
                                    </select>
                                    <div class="role-badge-display" style="margin-top: 4px;">
                                        <span class="role-badge role-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="status-verified">✓ Verified</span>
                                        <br>
                                        <small style="color: #6b7280;">
                                            {{ \Carbon\Carbon::parse($user->email_verified_at)->format('M d, Y') }}
                                        </small>
                                    @else
                                        <span class="status-unverified">✗ Not Verified</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $updatedUserName = \App\Models\User::find($user->updated_by)?->name ?? 'N/A';
                                    @endphp
                                    {{ $updatedUserName }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($user->updated_at)->format('M d, Y') }}
                                    <br>
                                    <small style="color: #6b7280;">
                                        {{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if($user->name == 'Super Admin')
                                            N/A
                                        @else
                                            <button onclick="editUser({{ $user->id }})" class="btn-edit" title="Edit User">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
                                        @endif
                                        @if($user->id != auth()->id())
                                            <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" class="btn-delete"
                                                title="Delete User">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <i class="fa-solid fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>No users found in the system.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Edit User</h3>
                <button onclick="closeEditModal()"
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form id="editUserForm">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name:</label>
                    <input type="text" id="editName"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email:</label>
                    <input type="email" id="editEmail"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;  text-transform: lowercase;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone:</label>
                    <input type="text" id="editPhone"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditModal()"
                        style="padding: 0.5rem 1rem; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.5rem 1rem; background: #991b1b; color: white; border: none; border-radius: 4px; cursor: pointer;">Update
                        User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Update user role with confirmation
        async function updateUserRole(selectElement) {
            const userId = selectElement.dataset.userId;
            const newRole = selectElement.value;
            const originalValue = selectElement.querySelector('option[selected]')?.value || selectElement.value;

            if (!confirm(`Are you sure you want to change the role to ${newRole}?`)) {
                selectElement.value = originalValue;
                return;
            }

            selectElement.classList.add('role-updating');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) throw new Error('CSRF token not found');

                const response = await fetch(`/admin/users/${userId}/role`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ role: newRole })
                });

                const data = await response.json();
                if (response.ok) {
                    const badgeElement = selectElement.parentElement.querySelector('.role-badge');
                    if (badgeElement) {
                        badgeElement.className = `role-badge role-${newRole}`;
                        badgeElement.textContent = newRole.charAt(0).toUpperCase() + newRole.slice(1);
                    }
                    showNotification('Role updated successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to update role');
                }
            } catch (error) {
                console.error('Error updating role:', error);
                selectElement.value = originalValue;
                showNotification(`Failed to update role. ${error.message}`, 'error');
            } finally {
                selectElement.classList.remove('role-updating');
            }
        }

        // Edit user function
        let currentEditUserId = null;

        function editUser(userId) {
            currentEditUserId = userId;
            document.getElementById('editUserModal').style.display = 'block';
            fetch(`/admin/users/edit/${userId}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(user => {
                    document.getElementById('editName').value = user.name || '';
                    document.getElementById('editEmail').value = user.email || '';
                    document.getElementById('editPhone').value = user.phoneno || '';
                })
                .catch(error => console.error('Error fetching user:', error));
        }

        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
            currentEditUserId = null;
        }

        // Handle edit form submission
        document.getElementById('editUserForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!currentEditUserId) return;

            const formData = {
                name: document.getElementById('editName').value,
                email: document.getElementById('editEmail').value,
                phoneno: document.getElementById('editPhone').value
            };

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) throw new Error('CSRF token not found');

                const response = await fetch(`/admin/users/update/${currentEditUserId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                if (response.ok) {
                    showNotification('User updated successfully!', 'success');
                    closeEditModal();
                    location.reload();
                } else {
                    throw new Error(data.message || `Failed to update user. Status: ${response.status}`);
                }
            } catch (error) {
                console.error('Error updating user:', error);
                const responseText = await response.text(); // Log raw response
                console.log('Response text:', responseText);
                showNotification(`Failed to update user. ${error.message}`, 'error');
            }
        });

        // Delete user function
        function deleteUser(userId, userName) {
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            showNotification('User deleted successfully!', 'success');
                            location.reload();
                        } else {
                            throw new Error('Failed to delete user');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting user:', error);
                        showNotification('Failed to delete user. Please try again.', 'error');
                    });
            }
        }

        // Notification function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                                                    position: fixed;
                                                    top: 20px;
                                                    right: 20px;
                                                    padding: 1rem 1.5rem;
                                                    border-radius: 6px;
                                                    color: white;
                                                    font-weight: 500;
                                                    z-index: 9999;
                                                    animation: slideIn 0.3s ease;
                                                    background: ${type === 'success' ? '#059669' : '#ef4444'};
                                                `;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Add CSS for notification animation
        const style = document.createElement('style');
        style.textContent = `
                                                @keyframes slideIn {
                                                    from { transform: translateX(100%); opacity: 0; }
                                                    to { transform: translateX(0); opacity: 1; }
                                                }
                                            `;
        document.head.appendChild(style);

        // Close modal when clicking outside
        document.getElementById('editUserModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
@endsection