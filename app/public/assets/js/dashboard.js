document.addEventListener('DOMContentLoaded', () => {
	initDashboardPage();
});

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
		.then((response) => {
			if (!response.ok) {
				throw new Error('Failed to load dashboard data');
			}

			return response.json();
		})
		.then((data) => {
			const dashboardKind = dashboardRoot.dataset.dashboardKind ?? data.dashboardKind ?? 'productionHouse';

			if (dashboardKind === 'freelancer') {
				renderFreelancerDashboard(dashboardRoot, data);
				return;
			}

			renderProductionHouseDashboard(dashboardRoot, data, dashboardKind);
		})
		.catch(() => {
			renderDashboardError(dashboardRoot);
		});
};

const escapeHtml = (value) => {
	const text = String(value ?? '');
	return text
		.replaceAll('&', '&amp;')
		.replaceAll('<', '&lt;')
		.replaceAll('>', '&gt;')
		.replaceAll('"', '&quot;')
		.replaceAll("'", '&#039;');
};

const renderDashboardError = (root) => {
	root.insertAdjacentHTML('afterbegin', '<div class="container"><div class="alert alert-warning">Live dashboard data could not be loaded right now.</div></div>');
};

const renderStatCards = (container, stats) => {
	const statCount = stats.length;
	const columnClass = statCount <= 1
		? 'col-12'
		: statCount === 2
			? 'col-12 col-md-6'
			: statCount === 3
				? 'col-12 col-md-6 col-xl-4'
				: 'col-12 col-sm-6 col-xl-3';

	container.innerHTML = stats.map((stat) => `
		<div class="${columnClass}">
			<div class="card border-0 shadow-sm rounded-4 text-center h-100">
				<div class="card-body">
					<p class="small text-uppercase mb-2" style="color: #1F2937;">${escapeHtml(stat.label ?? 'Stat')}</p>
					<p class="h3 mb-1" style="color: #172554;"${stat.id ? ` id="${escapeHtml(stat.id)}"` : ''}>${escapeHtml(stat.value ?? '0')}</p>
					<p class="small mb-0" style="color: #B91C1C;">${escapeHtml(stat.note ?? '')}</p>
				</div>
			</div>
		</div>
	`).join('');
};

const renderTableHead = (container, columns) => {
	container.innerHTML = `
		<tr>
			${columns.map((column) => `<th>${escapeHtml(column)}</th>`).join('')}
		</tr>
	`;
};

const renderListItems = (container, items) => {
	if (items.length === 0) {
		container.innerHTML = '<li class="list-group-item px-0 text-muted">No items available right now.</li>';
		return;
	}

	container.innerHTML = items.map((item) => `
		<li class="list-group-item px-0">
			<div class="d-flex justify-content-between align-items-start gap-2">
				<div>
					<p class="fw-semibold mb-0">${escapeHtml(item.title ?? '')}</p>
					<small style="color: #1F2937;">${escapeHtml(item.subtitle ?? '')}</small>
				</div>
				<small class="text-muted">${escapeHtml(item.meta ?? '')}</small>
			</div>
		</li>
	`).join('');
};

const renderProductionHouseDashboard = (root, data, dashboardKind) => {
	const userName = root.dataset.dashboardUserName ?? 'User';
	const badge = root.querySelector('[data-dashboard-badge]');
	const heroTitle = root.querySelector('[data-dashboard-hero-title]');
	const heroDescription = root.querySelector('[data-dashboard-hero-description]');
	const reviewLink = root.querySelector('[data-dashboard-review-link]');
	const statsContainer = root.querySelector('[data-dashboard-stats]');
	const primaryTitle = root.querySelector('[data-dashboard-primary-title]');
	const primaryHead = root.querySelector('[data-dashboard-primary-head]');
	const primaryBody = root.querySelector('[data-dashboard-primary-body]');
	const secondaryTitle = root.querySelector('[data-dashboard-secondary-title]');
	const secondaryList = root.querySelector('[data-dashboard-secondary-list]');

	if (badge) {
		badge.textContent = data.badgeLabel ?? 'Dashboard';
	}

	if (heroTitle) {
		heroTitle.textContent = `Welcome, ${userName}`;
	}

	if (heroDescription) {
		heroDescription.textContent = data.heroDescription ?? '';
	}

	if (reviewLink) {
		reviewLink.classList.toggle('d-none', dashboardKind !== 'productionHouse');
	}

	if (statsContainer) {
		renderStatCards(statsContainer, Array.isArray(data.stats) ? data.stats : []);
	}

	const primaryTable = data.primaryTable ?? {};
	if (primaryTitle) {
		primaryTitle.textContent = primaryTable.title ?? 'Current Gigs Overview';
	}
	if (primaryHead) {
		renderTableHead(primaryHead, Array.isArray(primaryTable.columns) ? primaryTable.columns : []);
	}
	if (primaryBody) {
		const rows = Array.isArray(primaryTable.rows) ? primaryTable.rows : [];
		primaryBody.innerHTML = rows.length === 0
			? '<tr><td colspan="4" class="text-center text-muted py-4">No gigs available right now.</td></tr>'
			: rows.map((row) => `
				<tr>
					<td>${escapeHtml(row.title ?? '')}</td>
					<td><span class="badge" style="background-color: #FBBF24; color: #172554;">${escapeHtml(row.status ?? '')}</span></td>
					<td>${escapeHtml(row.value ?? '0')}</td>
					<td>
						<a class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;" href="${escapeHtml(row.viewUrl ?? '/gigs')}">View</a>
						${row.canEdit ? `<a class="btn btn-sm btn-outline-secondary" href="${escapeHtml(row.editUrl ?? '/dashboard/gigs')}">Edit</a>` : ''}
					</td>
				</tr>
			`).join('');
	}

	if (secondaryTitle) {
		secondaryTitle.textContent = data.secondaryList?.title ?? 'Recent Applications';
	}
	if (secondaryList) {
		renderListItems(secondaryList, Array.isArray(data.secondaryList?.items) ? data.secondaryList.items : []);
	}
};

const renderFreelancerDashboard = (root, data) => {
	const userName = root.dataset.dashboardUserName ?? 'User';
	const badge = root.querySelector('[data-dashboard-badge]');
	const heroTitle = root.querySelector('[data-dashboard-hero-title]');
	const heroDescription = root.querySelector('[data-dashboard-hero-description]');
	const statsContainer = root.querySelector('[data-dashboard-stats]');
	const applicationsTitle = root.querySelector('[data-dashboard-applications-title]');
	const applicationsHead = root.querySelector('[data-dashboard-applications-head]');
	const applicationsBody = root.querySelector('[data-dashboard-applications-body]');
	const recommendedTitle = root.querySelector('[data-dashboard-recommended-title]');
	const recommendedHead = root.querySelector('[data-dashboard-recommended-head]');
	const recommendedBody = root.querySelector('[data-dashboard-recommended-body]');
	const recentTitle = root.querySelector('[data-dashboard-recent-title]');
	const recentList = root.querySelector('[data-dashboard-recent-list]');

	if (badge) {
		badge.textContent = data.badgeLabel ?? 'Dashboard';
	}

	if (heroTitle) {
		heroTitle.textContent = `Welcome, ${userName}`;
	}

	if (heroDescription) {
		heroDescription.textContent = data.heroDescription ?? '';
	}

	if (statsContainer) {
		renderStatCards(statsContainer, Array.isArray(data.stats) ? data.stats : []);
	}

	const applications = data.applications ?? {};
	if (applicationsTitle) {
		applicationsTitle.textContent = applications.title ?? 'My Applications';
	}
	if (applicationsHead) {
		renderTableHead(applicationsHead, Array.isArray(applications.columns) ? applications.columns : []);
	}
	if (applicationsBody) {
		const rows = Array.isArray(applications.rows) ? applications.rows : [];
		applicationsBody.innerHTML = rows.length === 0
			? '<tr><td colspan="6" class="text-center text-muted py-4">No applications yet.</td></tr>'
			: rows.map((row) => `
				<tr>
					<td>${escapeHtml(row.title ?? '')}</td>
					<td>${escapeHtml(row.category ?? '')}</td>
					<td>${escapeHtml(row.location ?? '')}</td>
					<td><span class="badge" style="background-color: #FBBF24; color: #172554;">${escapeHtml(row.status ?? '')}</span></td>
					<td>${escapeHtml(row.submittedAt ?? '')}</td>
					<td class="d-flex flex-wrap gap-2">
						<a class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;" href="${escapeHtml(row.detailUrl ?? '/gigs')}">View</a>
						${row.canWithdraw ? `
							<form method="post" action="/dashboard/submissions/${escapeHtml(row.submissionId ?? '')}/withdraw" class="d-inline">
								<button type="submit" class="btn btn-sm btn-outline-secondary">Withdraw</button>
							</form>
						` : ''}
					</td>
				</tr>
			`).join('');
	}

	const recommendedGigs = data.recommendedGigs ?? {};
	if (recommendedTitle) {
		recommendedTitle.textContent = recommendedGigs.title ?? 'Recommended Gigs';
	}
	if (recommendedHead) {
		renderTableHead(recommendedHead, Array.isArray(recommendedGigs.columns) ? recommendedGigs.columns : []);
	}
	if (recommendedBody) {
		const rows = Array.isArray(recommendedGigs.rows) ? recommendedGigs.rows : [];
		recommendedBody.innerHTML = rows.length === 0
			? '<tr><td colspan="5" class="text-center text-muted py-4">No recommended gigs available right now.</td></tr>'
			: rows.map((row) => `
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
			`).join('');
	}

	if (recentTitle) {
		recentTitle.textContent = data.recentActions?.title ?? 'Recent Application Updates';
	}
	if (recentList) {
		renderListItems(recentList, Array.isArray(data.recentActions?.items) ? data.recentActions.items : []);
	}
};
