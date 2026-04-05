/** Bootstraps the gigs listing page once the DOM is ready. */
document.addEventListener('DOMContentLoaded', onGigsListingReady);

let allGigs = [];

/** Starts the gigs listing and filter setup flow. */
function onGigsListingReady() {
	initGigsListing();
	initFilters();
}

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

/** Loads gig data from the API endpoint and stores it locally. */
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
		.then(handleGigsResponse)
		.then((data) => {
			allGigs = Array.isArray(data.gigs) ? data.gigs : [];
			loadGigsWithFilters();
		})
		.catch(() => {
			renderGigsError(gigContainer);
		});
};

/** Validates the gigs API response before parsing JSON. */
const handleGigsResponse = (response) => {
	if (!response.ok) {
		throw new Error('Failed to load gigs');
	}

	return response.json();
};

/** Initializes filter inputs and keeps the search state in sync. */
const initFilters = () => {
	syncSearchFromQuery();
	bindFilterChangeEvents();
	bindSearchEvents();
	bindRateSliderInput();
};

/** Reads the search query from the URL and applies it to the search input. */
const syncSearchFromQuery = () => {
	const queryParams = new URLSearchParams(window.location.search);
	const searchFromQuery = queryParams.get('search');

	if (searchFromQuery === null) {
		return;
	}

	const searchInput = document.getElementById('gigSearch');
	if (searchInput) {
		searchInput.value = searchFromQuery;
	}
};

/** Binds generic change handlers to all gig filter inputs. */
const bindFilterChangeEvents = () => {
	const filterInputs = document.querySelectorAll('#gigSearch, #gigDate, #gigLocation, #gigHourlyRate, [id^="category-"]');

	filterInputs.forEach((input) => {
		input.addEventListener('change', loadGigsWithFilters);
	});
};

/** Binds the live input handler for the search box. */
const bindSearchEvents = () => {
	const searchInput = document.getElementById('gigSearch');

	if (searchInput) {
		searchInput.addEventListener('input', loadGigsWithFilters);
	}
};

/** Binds the live input handler for the rate slider. */
const bindRateSliderInput = () => {
	const rateSlider = document.getElementById('gigHourlyRate');

	if (rateSlider) {
		rateSlider.addEventListener('input', loadGigsWithFilters);
	}
};

/** Reads the current filter state from the form controls. */
const getFilterParams = () => {
	const filters = {
		search: (document.getElementById('gigSearch')?.value ?? '').trim().toLowerCase(),
		location: (document.getElementById('gigLocation')?.value ?? '').trim(),
		date: document.getElementById('gigDate')?.value ?? '',
		minimumRate: document.getElementById('gigHourlyRate')?.value ?? '0',
		categories: [],
	};

	collectSelectedCategories(filters);
	return filters;
};

/** Collects all selected categories into the filters object. */
const collectSelectedCategories = (filters) => {
	const categoryCheckboxes = document.querySelectorAll('[id^="category-"]:checked');
	categoryCheckboxes.forEach((checkbox) => {
		filters.categories.push(checkbox.value);
	});
};

/** Filters the loaded gigs and renders the current result set. */
const loadGigsWithFilters = () => {
	const gigContainer = document.querySelector('[data-gigs-api-endpoint]');

	if (!gigContainer) {
		return;
	}

	const filters = getFilterParams();
	const minimumRate = Number(filters.minimumRate || '0');
	const filteredGigs = allGigs.filter((gig) => gigMatchesFilters(gig, filters, minimumRate));

	renderGigsListing(gigContainer, filteredGigs);
};

/** Normalizes category names so the UI filters stay consistent. */
const normalizeCategory = (category) => {
	const value = String(category ?? '').trim().toLowerCase();
	return value === 'audio' ? 'sound' : value;
};

/** Checks whether a gig matches all active filters. */
const gigMatchesFilters = (gig, filters, minimumRate) => {
	if (!gigMatchesTextFilter(gig, filters.search)) {
		return false;
	}

	if (!gigMatchesLocationFilter(gig, filters.location)) {
		return false;
	}

	if (!gigMatchesDateFilter(gig, filters.date)) {
		return false;
	}

	if (!gigMatchesRateFilter(gig, minimumRate)) {
		return false;
	}

	if (!gigMatchesCategoryFilter(gig, filters.categories)) {
		return false;
	}

	return true;
};

/** Checks whether the gig matches the text search filter. */
const gigMatchesTextFilter = (gig, search) => {
	if (!search) {
		return true;
	}

	const title = String(gig.title ?? '').toLowerCase();
	const description = String(gig.description ?? '').toLowerCase();
	return title.includes(search) || description.includes(search);
};

/** Checks whether the gig matches the location filter. */
const gigMatchesLocationFilter = (gig, location) => {
	if (!location) {
		return true;
	}

	return String(gig.location ?? '').toLowerCase().includes(location.toLowerCase());
};

/** Checks whether the gig matches the selected date filter. */
const gigMatchesDateFilter = (gig, date) => {
	if (!date) {
		return true;
	}

	return String(gig.startDate ?? '') >= date;
};

/** Checks whether the gig matches the minimum rate filter. */
const gigMatchesRateFilter = (gig, minimumRate) => {
	if (Number.isNaN(minimumRate) || minimumRate <= 0) {
		return true;
	}

	return Number(gig.payRate ?? 0) >= minimumRate;
};

/** Checks whether the gig matches the selected categories filter. */
const gigMatchesCategoryFilter = (gig, categories) => {
	if (categories.length === 0) {
		return true;
	}

	return categories.includes(normalizeCategory(gig.category));
};

/** Renders the filtered gigs and the result counter. */
const renderGigsListing = (container, gigs) => {
	updateGigsResultCount(gigs.length);
	container.innerHTML = gigs.length === 0 ? renderNoGigMatchesState() : renderGigCards(gigs);
};

/** Updates the visible result count for the listing. */
const updateGigsResultCount = (count) => {
	const resultCount = document.querySelector('#gigsResultCount');

	if (resultCount) {
		resultCount.textContent = `${count} results`;
	}
};

/** Renders the empty state shown when no gigs match the filters. */
const renderNoGigMatchesState = () => `
	<div class="col-12">
		<div class="card border-0 shadow-sm rounded-4">
			<div class="card-body text-center py-5">
				<p class="text-muted mb-0">No gigs match your filters.</p>
			</div>
		</div>
	</div>
`;

/** Renders all gig cards for the current filtered list. */
const renderGigCards = (gigs) => gigs.map((gig) => renderGigCard(gig)).join('');

/** Renders a single gig card. */
const renderGigCard = (gig) => `
	<article class="col-12 col-md-6">
		<div class="card border-0 shadow-sm rounded-4 h-100">
			<img src="${escapeHtml(gig.imageUrl ?? '/assets/images/placeholder.svg')}" class="card-img-top rounded-top-4" alt="${escapeHtml(gig.title ?? 'Gig')}"
				style="height: 180px; object-fit: cover;">
			<div class="card-body p-4 d-flex flex-column">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="badge" style="background-color: #FBBF24; color: #172554;">${escapeHtml(gig.category ?? '')}</span>
					<span class="fw-semibold" style="color: #172554;">${escapeHtml(renderGigRateLabel(gig))}</span>
				</div>
				<h3 class="h5 fw-bold mb-2" style="color: #172554;">${escapeHtml(gig.title ?? 'Gig Title')}</h3>
				<p class="mb-3" style="color: #1F2937;">${escapeHtml(gig.description ?? '')}</p>
				<p class="small mb-1" style="color: #1F2937;"><i class="fa-solid fa-location-dot me-2"></i>${escapeHtml(gig.location ?? '')}</p>
				<p class="small mb-3" style="color: #1F2937;"><i class="fa-regular fa-calendar me-2"></i>${escapeHtml(gig.startDate ?? '')}</p>
				<a href="${escapeHtml(gig.detailUrl ?? `/gigs/${gig.gigId}`)}" class="btn mt-auto text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
			</div>
		</div>
	</article>
`;

/** Builds the formatted pay-rate label shown on each gig card. */
const renderGigRateLabel = (gig) => {
	if (!gig.payRate) {
		return 'TBD';
	}

	return `EUR ${gig.payRate}/${gig.rateType === 'fixed' ? 'project' : 'hour'}`;
};

/** Renders the error state when gigs cannot be loaded. */
const renderGigsError = (container) => {
	container.innerHTML = renderGigsErrorMarkup();
};

/** Returns the markup shown when the gigs API request fails. */
const renderGigsErrorMarkup = () => `
	<div class="col-12">
		<div class="card border-0 shadow-sm rounded-4">
			<div class="card-body text-center py-5">
				<p class="text-muted mb-0">Could not load gigs right now. Please try again later.</p>
			</div>
		</div>
	</div>
`;
