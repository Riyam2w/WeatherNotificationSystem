<section class="alerts-page">

    <!-- Header -->
    <div class="alerts-header">
        <div>
            <h1 class="page-title">My Alerts</h1>
            <p class="page-subtitle">
                Manage your active weather monitoring and notification settings.
            </p>
        </div>

        <button class="btn btn-primary" id="createAlertBtn">
            + Create Alert
        </button>
    </div>

    <!-- Filters -->
    <div class="alerts-filters">
        <input
            type="text"
            id="alertSearch"
            class="filter-input"
            placeholder="Search locations..."
        >

        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="paused">Paused</option>
            <option value="triggered">Triggered</option>
        </select>

        <div class="filter-right">
            <label>Sort by:</label>
            <select id="sortBy" class="filter-select">
                <option value="created_at">Date Created</option>
                <option value="city">City</option>
            </select>
        </div>
    </div>

    <!-- Alerts Table -->
    <div class="alerts-table-wrap">
        <table class="alerts-table">
            <thead>
                <tr>
                    <th>City / Location</th>
                    <th>Condition</th>
                    <th>Threshold</th>
                    <th>Current Value</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="alertsTableBody">
                <tr>
                    <td colspan="6" class="empty-state">
                        Loading alerts...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</section>
