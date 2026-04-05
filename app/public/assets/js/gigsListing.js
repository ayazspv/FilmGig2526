document.addEventListener('DOMContentLoaded', () => {
	initGigsListing();
	initFilters();
});

let allGigs = [];

const escapeHtml = (value) => {
	const text = String(value ?? '');
	return text
		.replaceAll('&', '&amp;')
		.replaceAll('<', '&lt;')
		.replaceAll('>', '&gt;')
		.replaceAll('"', '&quot;')
		.replaceAll("'", '&#039;');
};

const initGigsListing = () => {
	const gigContainer = document.querySelector('[data-gigs-api-endpoint]');

	if (!gigContainer) {
		return;
	}

	const endpoint = gigContainer.dataset.gigsApiEndpoint;
	if (!endpoint) {
		return;
	}

	fetch(endpoint, { headers: { Accept: 'application/json' } })
		.then((response) => {
			if (!response.ok) {
				throw new Error('Failed to load gigs');
			}

			return response.json();
		})
		.then((data) => {
			allGigs = Array.isArray(data.gigs) ? data.gigs : [];
			loadGigsWithFilters();
		})
		.catch(() => {
			renderGigsError(gigContainer);
		});
};

const initFilters = () => {
	const queryParams = new URLSearchParams(window.location.search);
	const searchFromQuery = queryParams.get('search');
	if (searchFromQuery !== null) {
		const searchInput = document.getElementById('gigSearch');
		if (searchInput) {
			searchInput.value = searchFromQuery;
		}
	}

	const filterInputs = document.querySelectorAll('#gigSearch, #gigDate, #gigLocation, #gigHourlyRate, [id^="category-"]');

	filterInputs.forEach((input) => {
		input.addEventListener('change', () => {
			loadGigsWithFilters();
		});
	});

	// Also listen to input events for range slider
	const rateSlider = document.getElementById('gigHourlyRate');
	if (rateSlider) {
		rateSlider.addEventListener('input', () => {
			loadGigsWithFilters();
		});
	}

	const searchInput = document.getElementById('gigSearch');
	if (searchInput) {
		searchInput.addEventListener('input', () => {
			loadGigsWithFilters();
		});
	}
};

const getFilterParams = () => {
	const filters = {
		search: (document.getElementById('gigSearch')?.value ?? '').trim().toLowerCase(),
		location: (document.getElementById('gigLocation')?.value ?? '').trim(),
		date: document.getElementById('gigDate')?.value ?? '',
		minimumRate: document.getElementById('gigHourlyRate')?.value ?? '0',
		categories: [],
	};

	// Collect selected categories
	const categoryCheckboxes = document.querySelectorAll('[id^="category-"]:checked');
	categoryCheckboxes.forEach((checkbox) => {
		filters.categories.push(checkbox.value);
	});

	return filters;
};

const loadGigsWithFilters = () => {
	const gigContainer = document.querySelector('[data-gigs-api-endpoint]');

	if (!gigContainer) {
		return;
	}

	const filters = getFilterParams();
	const minimumRate = Number(filters.minimumRate || '0');
	const normalizeCategory = (category) => {
		const value = String(category ?? '').trim().toLowerCase();
		return value === 'audio' ? 'sound' : value;
	};

	const filteredGigs = allGigs.filter((gig) => {
		if (filters.search) {
			const title = String(gig.title ?? '').toLowerCase();
			const description = String(gig.description ?? '').toLowerCase();

			if (!title.includes(filters.search) && !description.includes(filters.search)) {
				return false;
			}
		}

		if (filters.location && !String(gig.location ?? '').toLowerCase().includes(filters.location.toLowerCase())) {
			return false;
		}

		if (filters.date && String(gig.startDate ?? '') < filters.date) {
			return false;
		}

		if (!Number.isNaN(minimumRate) && minimumRate > 0 && Number(gig.payRate ?? 0) < minimumRate) {
			return false;
		}

		if (filters.categories.length > 0 && !filters.categories.includes(normalizeCategory(gig.category))) {
			return false;
		}

		return true;
	});

	renderGigsListing(gigContainer, filteredGigs);
};

const renderGigsListing = (container, gigs) => {
	const resultCount = document.querySelector('#gigsResultCount');

	if (resultCount) {
		resultCount.textContent = `${gigs.length} results`;
	}

	if (gigs.length === 0) {
		container.innerHTML = `
			<div class="col-12">
				<div class="card border-0 shadow-sm rounded-4">
					<div class="card-body text-center py-5">
						<p class="text-muted mb-0">No gigs match your filters.</p>
					</div>
				</div>
			</div>
		`;
		return;
	}

	container.innerHTML = gigs.map((gig) => `
		<article class="col-12 col-md-6">
			<div class="card border-0 shadow-sm rounded-4 h-100">
				<img src="${escapeHtml(gig.imageUrl ?? '/assets/images/placeholder.svg')}" class="card-img-top rounded-top-4" alt="${escapeHtml(gig.title ?? 'Gig')}"
					style="height: 180px; object-fit: cover;">
				<div class="card-body p-4 d-flex flex-column">
					<div class="d-flex justify-content-between align-items-center mb-2">
						<span class="badge" style="background-color: #FBBF24; color: #172554;">${escapeHtml(gig.category ?? '')}</span>
						<span class="fw-semibold" style="color: #172554;">${escapeHtml(
							gig.payRate
								? `EUR ${gig.payRate}/${gig.rateType === 'fixed' ? 'project' : 'hour'}`
								: 'TBD'
						)}</span>
					</div>
					<h3 class="h5 fw-bold mb-2" style="color: #172554;">${escapeHtml(gig.title ?? 'Gig Title')}</h3>
					<p class="mb-3" style="color: #1F2937;">${escapeHtml(gig.description ?? '')}</p>
					<p class="small mb-1" style="color: #1F2937;"><i class="fa-solid fa-location-dot me-2"></i>${escapeHtml(gig.location ?? '')}</p>
					<p class="small mb-3" style="color: #1F2937;"><i class="fa-regular fa-calendar me-2"></i>${escapeHtml(gig.startDate ?? '')}</p>
					<a href="${escapeHtml(gig.detailUrl ?? `/gigs/${gig.gigId}`)}" class="btn mt-auto text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
				</div>
			</div>
		</article>
	`).join('');
};

const renderGigsError = (container) => {
	container.innerHTML = `
		<div class="col-12">
			<div class="card border-0 shadow-sm rounded-4">
				<div class="card-body text-center py-5">
					<p class="text-muted mb-0">Could not load gigs right now. Please try again later.</p>
				</div>
			</div>
		</div>
	`;
};
