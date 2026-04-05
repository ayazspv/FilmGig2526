/** Bootstraps the dashboard once the page is ready. */
document.addEventListener('DOMContentLoaded', onDashboardReady);

/** Starts the dashboard initialization flow. */
function onDashboardReady() {
	initDashboardPage();
}

/** Loads dashboard data and renders the correct dashboard variant. */
const initDashboardPage = () => {
	const dashboardRoot = document.querySelector('[data-dashboard-root]');

	if (!dashboardRoot) {
		return;
	}

	const endpoint = dashboardRoot.dataset.dashboardApiEndpoint;
	if (!endpoint) {
		return;
	}

	fetch(endpoint, { headers: { Accept: 'application/json' } })
		.then(handleDashboardResponse)
		.then((data) => renderDashboardVariant(dashboardRoot, data))
		.catch(() => {
			renderDashboardError(dashboardRoot);
		});
};

/** Validates the dashboard response before parsing JSON. */
const handleDashboardResponse = (response) => {
	if (!response.ok) {
		throw new Error('Failed to load dashboard data');
	}

	return response.json();
};

/** Escapes user-provided values before inserting them into HTML. */
const escapeHtml = (value) => {
	const text = String(value ?? '');
	return text
		.replaceAll('&', '&amp;')
		.replaceAll('<', '&lt;')
		.replaceAll('>', '&gt;')
		.replaceAll('"', '&quot;')
		.replaceAll("'", '&#039;');
};

/** Returns an array when the input is an array, otherwise an empty array. */
const ensureArray = (value) => (Array.isArray(value) ? value : []);

/** Renders the dashboard fallback message when live data fails. */
const renderDashboardError = (root) => {
	root.insertAdjacentHTML('afterbegin', '<div class="container"><div class="alert alert-warning">Live dashboard data could not be loaded right now.</div></div>');
};

/** Renders stat cards using a layout that matches the available card count. */
const renderStatCards = (container, stats) => {
	const statCount = stats.length;
	const columnClass = statCount <= 1
		? 'col-12'
		: statCount === 2
			? 'col-12 col-md-6'
			: statCount === 3
				? 'col-12 col-md-6 col-xl-4'
				: 'col-12 col-sm-6 col-xl-3';

	container.innerHTML = stats.map((stat) => renderStatCard(columnClass, stat)).join('');
};

/** Renders a single stat card. */
const renderStatCard = (columnClass, stat) => `
	<div class="${columnClass}">
		<div class="card border-0 shadow-sm rounded-4 text-center h-100">
			<div class="card-body">
				<p class="small text-uppercase mb-2" style="color: #1F2937;">${escapeHtml(stat.label ?? 'Stat')}</p>
				<p class="h3 mb-1" style="color: #172554;"${stat.id ? ` id="${escapeHtml(stat.id)}"` : ''}>${escapeHtml(stat.value ?? '0')}</p>
				<p class="small mb-0" style="color: #B91C1C;">${escapeHtml(stat.note ?? '')}</p>
			</div>
		</div>
	</div>
`;

/** Renders a table header row from a list of column labels. */
const renderTableHead = (container, columns) => {
	container.innerHTML = `
		<tr>
			${columns.map((column) => `<th>${escapeHtml(column)}</th>`).join('')}
		</tr>
	`;
};

/** Renders a list block with a fallback state when no items exist. */
const renderListItems = (container, items) => {
	if (items.length === 0) {
		container.innerHTML = '<li class="list-group-item px-0 text-muted">No items available right now.</li>';
		return;
	}

	container.innerHTML = items.map((item) => renderListItem(item)).join('');
};

/** Renders a single list item entry. */
const renderListItem = (item) => `
	<li class="list-group-item px-0">
		<div class="d-flex justify-content-between align-items-start gap-2">
			<div>
				<p class="fw-semibold mb-0">${escapeHtml(item.title ?? '')}</p>
				<small style="color: #1F2937;">${escapeHtml(item.subtitle ?? '')}</small>
			</div>
			<small class="text-muted">${escapeHtml(item.meta ?? '')}</small>
		</div>
	</li>
`;

/** Chooses and renders the correct dashboard variant. */
const renderDashboardVariant = (root, data) => {
	const dashboardKind = root.dataset.dashboardKind ?? data.dashboardKind ?? 'productionHouse';

	if (dashboardKind === 'freelancer') {
		renderFreelancerDashboard(root, data);
		return;
	}

	renderProductionHouseDashboard(root, data, dashboardKind);
};

/** Renders the shared production-house dashboard sections. */
const renderProductionHouseDashboard = (root, data, dashboardKind) => {
	renderDashboardHeader(root, data);
	renderProductionHouseStats(root, data);
	renderProductionHousePrimaryTable(root, data);
	renderProductionHouseSecondaryList(root, data);
	renderReviewLink(root, dashboardKind);
};

/** Renders the shared dashboard header content. */
const renderDashboardHeader = (root, data) => {
	const userName = root.dataset.dashboardUserName ?? 'User';
	const badge = root.querySelector('[data-dashboard-badge]');
	const heroTitle = root.querySelector('[data-dashboard-hero-title]');
	const heroDescription = root.querySelector('[data-dashboard-hero-description]');

	if (badge) {
		badge.textContent = data.badgeLabel ?? 'Dashboard';
	}

	if (heroTitle) {
		heroTitle.textContent = `Welcome, ${userName}`;
	}

	if (heroDescription) {
		heroDescription.textContent = data.heroDescription ?? '';
	}
};

/** Toggles the production-house review link visibility. */
const renderReviewLink = (root, dashboardKind) => {
	const reviewLink = root.querySelector('[data-dashboard-review-link]');

	if (reviewLink) {
		reviewLink.classList.toggle('d-none', dashboardKind !== 'productionHouse');
	}
};

/** Renders production-house statistics. */
const renderProductionHouseStats = (root, data) => {
	const statsContainer = root.querySelector('[data-dashboard-stats]');

	if (statsContainer) {
		renderStatCards(statsContainer, ensureArray(data.stats));
	}
};

/** Renders the production-house gig table. */
const renderProductionHousePrimaryTable = (root, data) => {
	const primaryTable = data.primaryTable ?? {};
	const primaryTitle = root.querySelector('[data-dashboard-primary-title]');
	const primaryHead = root.querySelector('[data-dashboard-primary-head]');
	const primaryBody = root.querySelector('[data-dashboard-primary-body]');

	if (primaryTitle) {
		primaryTitle.textContent = primaryTable.title ?? 'Current Gigs Overview';
	}

	if (primaryHead) {
		renderTableHead(primaryHead, ensureArray(primaryTable.columns));
	}

	if (primaryBody) {
		primaryBody.innerHTML = renderProductionHouseRows(primaryTable.rows);
	}
};

/** Renders production-house table rows. */
const renderProductionHouseRows = (rowsInput) => {
	const rows = ensureArray(rowsInput);

	if (rows.length === 0) {
		return '<tr><td colspan="4" class="text-center text-muted py-4">No gigs available right now.</td></tr>';
	}

	return rows.map((row) => renderProductionHouseRow(row)).join('');
};

/** Renders a single production-house gig row. */
const renderProductionHouseRow = (row) => `
	<tr>
		<td>${escapeHtml(row.title ?? '')}</td>
		<td><span class="badge" style="background-color: #FBBF24; color: #172554;">${escapeHtml(row.status ?? '')}</span></td>
		<td>${escapeHtml(row.value ?? '0')}</td>
		<td>
			<a class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;" href="${escapeHtml(row.viewUrl ?? '/gigs')}">View</a>
			${row.canEdit ? `<a class="btn btn-sm btn-outline-secondary" href="${escapeHtml(row.editUrl ?? '/dashboard/gigs')}">Edit</a>` : ''}
		</td>
	</tr>
`;

/** Renders the production-house secondary activity list. */
const renderProductionHouseSecondaryList = (root, data) => {
	const secondaryTitle = root.querySelector('[data-dashboard-secondary-title]');
	const secondaryList = root.querySelector('[data-dashboard-secondary-list]');

	if (secondaryTitle) {
		secondaryTitle.textContent = data.secondaryList?.title ?? 'Recent Applications';
	}

	if (secondaryList) {
		renderListItems(secondaryList, ensureArray(data.secondaryList?.items));
	}
};

/** Renders the freelancer dashboard sections. */
const renderFreelancerDashboard = (root, data) => {
	renderDashboardHeader(root, data);
	renderFreelancerStats(root, data);
	renderFreelancerApplications(root, data);
	renderFreelancerRecommendedGigs(root, data);
	renderFreelancerRecentActions(root, data);
};

/** Renders freelancer statistics. */
const renderFreelancerStats = (root, data) => {
	const statsContainer = root.querySelector('[data-dashboard-stats]');

	if (statsContainer) {
		renderStatCards(statsContainer, ensureArray(data.stats));
	}
};

/** Renders the freelancer applications table. */
const renderFreelancerApplications = (root, data) => {
	const applications = data.applications ?? {};
	const applicationsTitle = root.querySelector('[data-dashboard-applications-title]');
	const applicationsHead = root.querySelector('[data-dashboard-applications-head]');
	const applicationsBody = root.querySelector('[data-dashboard-applications-body]');

	if (applicationsTitle) {
		applicationsTitle.textContent = applications.title ?? 'My Applications';
	}

	if (applicationsHead) {
		renderTableHead(applicationsHead, ensureArray(applications.columns));
	}

	if (applicationsBody) {
		applicationsBody.innerHTML = renderFreelancerApplicationRows(applications.rows);
	}
};

/** Renders freelancer application rows. */
const renderFreelancerApplicationRows = (rowsInput) => {
	const rows = ensureArray(rowsInput);

	if (rows.length === 0) {
		return '<tr><td colspan="6" class="text-center text-muted py-4">No applications yet.</td></tr>';
	}

	return rows.map((row) => renderFreelancerApplicationRow(row)).join('');
};

/** Renders a single freelancer application row. */
const renderFreelancerApplicationRow = (row) => `
	<tr>
		<td>${escapeHtml(row.title ?? '')}</td>
		<td>${escapeHtml(row.category ?? '')}</td>
		<td>${escapeHtml(row.location ?? '')}</td>
		<td><span class="badge" style="background-color: #FBBF24; color: #172554;">${escapeHtml(row.status ?? '')}</span></td>
		<td>${escapeHtml(row.submittedAt ?? '')}</td>
		<td class="d-flex flex-wrap gap-2">
			<a class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;" href="${escapeHtml(row.detailUrl ?? '/gigs')}">View</a>
			${renderWithdrawAction(row)}
		</td>
	</tr>
`;

/** Renders the optional withdrawal action for an application row. */
const renderWithdrawAction = (row) => {
	if (!row.canWithdraw) {
		return '';
	}

	return `
		<form method="post" action="/dashboard/submissions/${escapeHtml(row.submissionId ?? '')}/withdraw" class="d-inline">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Withdraw</button>
		</form>
	`;
};

/** Renders the freelancer recommended gigs table. */
const renderFreelancerRecommendedGigs = (root, data) => {
	const recommendedGigs = data.recommendedGigs ?? {};
	const recommendedTitle = root.querySelector('[data-dashboard-recommended-title]');
	const recommendedHead = root.querySelector('[data-dashboard-recommended-head]');
	const recommendedBody = root.querySelector('[data-dashboard-recommended-body]');

	if (recommendedTitle) {
		recommendedTitle.textContent = recommendedGigs.title ?? 'Recommended Gigs';
	}

	if (recommendedHead) {
		renderTableHead(recommendedHead, ensureArray(recommendedGigs.columns));
	}

	if (recommendedBody) {
		recommendedBody.innerHTML = renderRecommendedGigRows(recommendedGigs.rows);
	}
};

/** Renders recommended gig rows. */
const renderRecommendedGigRows = (rowsInput) => {
	const rows = ensureArray(rowsInput);

	if (rows.length === 0) {
		return '<tr><td colspan="5" class="text-center text-muted py-4">No recommended gigs available right now.</td></tr>';
	}

	return rows.map((row) => renderRecommendedGigRow(row)).join('');
};

/** Renders a single recommended gig row. */
const renderRecommendedGigRow = (row) => `
	<tr>
		<td>${escapeHtml(row.title ?? '')}</td>
		<td>${escapeHtml(row.category ?? '')}</td>
		<td>${escapeHtml(row.location ?? '')}</td>
		<td>${escapeHtml(row.value ?? '')}</td>
		<td class="d-flex flex-wrap gap-2">
			<a class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;" href="${escapeHtml(row.applyUrl ?? row.detailUrl ?? '/gigs')}">Apply</a>
			<a class="btn btn-sm btn-outline-secondary" href="${escapeHtml(row.detailUrl ?? '/gigs')}">View</a>
		</td>
	</tr>
`;

/** Renders the freelancer recent activity list. */
const renderFreelancerRecentActions = (root, data) => {
	const recentTitle = root.querySelector('[data-dashboard-recent-title]');
	const recentList = root.querySelector('[data-dashboard-recent-list]');

	if (recentTitle) {
		recentTitle.textContent = data.recentActions?.title ?? 'Recent Application Updates';
	}

	if (recentList) {
		renderListItems(recentList, ensureArray(data.recentActions?.items));
	}
};
