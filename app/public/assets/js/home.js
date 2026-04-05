document.addEventListener('DOMContentLoaded', () => {
	initHomePage();
});

const initHomePage = () => {
	const homeApp = document.querySelector('[data-home-api-endpoint]');

	if (!homeApp) {
		return;
	}

	const endpoint = homeApp.dataset.homeApiEndpoint;
	if (!endpoint) {
		return;
	}

	fetch(endpoint, { headers: { Accept: 'application/json' } })
		.then((response) => {
			if (!response.ok) {
				throw new Error('Failed to load home data');
			}

			return response.json();
		})
		.then((data) => {
			renderHomeHero(data.hero ?? {});
			renderHomeHowItWorks(data.howItWorks ?? {});
			renderHomeGigs(data.gigs ?? {});
			renderHomeWhyFilmGig(data.whyFilmGig ?? {});
		})
		.catch(() => {
			renderHomeFallbackMessage();
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

const renderHomeHero = (hero) => {
	const heroRoot = document.querySelector('[data-home-hero]');
	if (!heroRoot) {
		return;
	}

	const heroTitle = escapeHtml(hero.title ?? 'Find Your Next Film Gig in Seconds!').replaceAll('\n', '<br>');
	const heroSubtitle = escapeHtml(hero.subtitle ?? 'Browse live gigs and platform activity.');
	const heroCta = hero.cta ?? { url: '/signup', label: 'Start Your Journey' };
	const heroImage = hero.image ?? {};
	const heroStats = Array.isArray(hero.stats) ? hero.stats : [];
	const searchPlaceholder = escapeHtml(hero.searchPlaceholder ?? 'Search for gigs');
	const searchButtonLabel = escapeHtml(hero.searchButtonLabel ?? 'Search');

	heroRoot.innerHTML = `
		<div class="container">
			<div class="row g-4 align-items-center">
				<div class="col-lg-6">
					<span class="badge text-uppercase mb-3" style="background-color: #FBBF24; color: #172554;">Built for film creators</span>
					<h1 class="display-5 fw-bold" style="color: #172554;">${heroTitle}</h1>
					<p class="lead mb-4" style="color: #1F2937;">${heroSubtitle}</p>

					<div class="bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4">
						<div class="row g-2">
							<div class="col-12 col-md-9">
								<input type="text" class="form-control form-control-lg" placeholder="${searchPlaceholder}">
							</div>
							<div class="col-6 col-md-3 d-grid">
								<button class="btn btn-lg text-white" type="button" style="background-color: #B91C1C; border-color: #B91C1C;">${searchButtonLabel}</button>
							</div>
						</div>
					</div>

					<a class="btn btn-lg text-white fw-semibold" style="background-color: #172554; border-color: #172554;" href="${escapeHtml(heroCta.url ?? '/signup')}">${escapeHtml(heroCta.label ?? 'Start Your Journey')}</a>

					${heroStats.length > 0 ? `<div class="row g-3 mt-4">${heroStats.map((stat) => `
						<div class="col-6 col-md-3">
							<div class="bg-white rounded-4 shadow-sm p-3 h-100">
								<div class="small text-uppercase" style="color: #1F2937;">${escapeHtml(stat.label ?? 'Stat')}</div>
								<div class="h4 fw-bold mb-1" style="color: #172554;">${escapeHtml(stat.value ?? '0')}</div>
								<div class="small" style="color: #B91C1C;">${escapeHtml(stat.note ?? '')}</div>
							</div>
						</div>
					`).join('')}</div>` : ''}
				</div>
				<div class="col-lg-6">
					<div class="row g-3">
						<div class="col-12">
							<img src="${escapeHtml(heroImage.src ?? 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=900&q=80')}" class="img-fluid rounded-4 shadow main-hero-img w-100" alt="${escapeHtml(heroImage.alt ?? 'Hero Image')}">
						</div>
						<div class="col-6">
							<img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=700&q=80" class="img-fluid rounded-4 shadow-sm main-hero-img w-100" alt="Camera operator on set">
						</div>
						<div class="col-6">
							<img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=700&q=80" class="img-fluid rounded-4 shadow-sm main-hero-img w-100" alt="Film editor working">
						</div>
					</div>
				</div>
			</div>
		</div>
	`;
};

const renderHomeHowItWorks = (howItWorks) => {
	const section = document.querySelector('[data-home-how-it-works]');
	if (!section) {
		return;
	}

	const steps = Array.isArray(howItWorks.steps) ? howItWorks.steps : [];
	section.innerHTML = `
		<div class="container">
			<div class="text-center mb-5">
				<h2 class="fw-bold" style="color: #172554;">${escapeHtml(howItWorks.title ?? 'How It Works')}</h2>
				<p class="mb-0" style="color: #1F2937;">From discovery to getting hired, move through the flow in minutes.</p>
			</div>
			<div class="d-flex flex-column flex-md-row align-items-stretch justify-content-center gap-3 gap-md-2">
				${steps.map((step, index) => `
					<article class="card border-0 shadow-sm rounded-4 text-center p-3 flex-fill" style="min-width: 0;">
						<div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #E5E7EB; color: #172554;">
							<i class="${escapeHtml(step.icon ?? 'fa-solid fa-circle')} fs-4"></i>
						</div>
						<h3 class="h5 fw-bold">${escapeHtml(step.title ?? 'Step')}</h3>
						<p class="mb-0" style="color: #1F2937;">${escapeHtml(step.description ?? '')}</p>
					</article>
					${index < steps.length - 1 ? `<div class="d-flex flex-column justify-content-center align-items-center px-md-2 py-2 py-md-0"><i class="fa-solid fa-arrow-right d-none d-md-block fs-5" style="color: #B91C1C;"></i><i class="fa-solid fa-arrow-down d-md-none fs-4" style="color: #B91C1C;"></i></div>` : ''}
				`).join('')}
			</div>
		</div>
	`;
};

const renderHomeGigs = (gigs) => {
	const section = document.querySelector('[data-home-gigs]');
	if (!section) {
		return;
	}

	const items = Array.isArray(gigs.items) ? gigs.items : [];
	const subtitle = escapeHtml(gigs.subtitle ?? 'Browse the latest gigs from the database.');
	const latestItems = items.slice(0, 3);

	section.innerHTML = `
		<div class="container">
			<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
				<div>
					<h2 class="fw-bold mb-2" style="color: #172554;">${escapeHtml(gigs.title ?? 'Available Gigs')}</h2>
					<p class="mb-0" style="color: #1F2937;">${subtitle}</p>
				</div>
				<div>
					<span class="badge" style="background-color: #172554;">Newest opportunities</span>
				</div>
			</div>

			${latestItems.length === 0 ? `
				<div class="card border-0 shadow-sm rounded-4">
					<div class="card-body text-center py-5">
						<p class="text-muted mb-0">No gigs available right now.</p>
					</div>
				</div>
			` : `
				<div class="row g-4 mb-5">
					${latestItems.map((item) => `
						<div class="col-12 col-md-6 col-lg-4 d-flex">
							<article class="card border-0 shadow-sm rounded-4 w-100 overflow-hidden">
								<img src="${escapeHtml(item.thumbnail ?? item.imageUrl ?? 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=900&q=80')}" class="card-img-top gigs-thumbnail" alt="${escapeHtml(item.title ?? 'Gig Thumbnail')}">
								<div class="card-body d-flex flex-column">
									<h3 class="h5 card-title fw-bold mb-2">${escapeHtml(item.title ?? 'Gig Title')}</h3>
									<p class="card-text flex-grow-1" style="color: #1F2937;">${escapeHtml(item.description ?? '')}</p>
									<div class="mt-auto d-flex justify-content-between align-items-center pt-2">
										<span class="fw-bold" style="color: #B91C1C;">${escapeHtml(item.price ?? '')}</span>
										<a href="${escapeHtml(item.applyUrl ?? '/gigs')}" class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
									</div>
								</div>
							</article>
						</div>
					`).join('')}
				</div>

				<div class="text-center">
					<a href="/gigs" class="btn btn-lg text-white fw-bold" style="background-color: #B91C1C; border-color: #B91C1C;">All the gigs</a>
				</div>
			`}
		</div>
	`;
};

const renderHomeWhyFilmGig = (whyFilmGig) => {
	const section = document.querySelector('[data-home-why-filmgig]');
	if (!section) {
		return;
	}

	const bullets = Array.isArray(whyFilmGig.bullets) ? whyFilmGig.bullets : [];
	const whyImage = whyFilmGig.image ?? {};

	section.innerHTML = `
		<div class="container">
			<div class="row g-4 align-items-center">
				<div class="col-lg-6 text-white">
					<h2 class="fw-bold mb-3">${escapeHtml(whyFilmGig.title ?? 'Why FilmGig?')}</h2>
					<p class="mb-4">${escapeHtml(whyFilmGig.description ?? '')}</p>
					<div class="row g-3">
						${bullets.map((bullet) => `
							<div class="col-12">
								<div class="d-flex align-items-start gap-2 bg-white bg-opacity-10 rounded-3 p-3">
									<i class="fa-solid fa-check mt-1" style="color: #FBBF24;"></i>
									<span>${escapeHtml(bullet)}</span>
								</div>
							</div>
						`).join('')}
					</div>
				</div>

				<div class="col-lg-6">
					<div class="row g-3">
						<div class="col-12">
							<img src="${escapeHtml(whyImage.src ?? 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80')}" class="img-fluid rounded-4 shadow main-hero-img w-100" alt="${escapeHtml(whyImage.alt ?? 'Why FilmGig')}">
						</div>
						<div class="col-6">
							<img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-sm main-hero-img w-100" alt="Filmmaking collaboration">
						</div>
						<div class="col-6">
							<img src="https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-sm main-hero-img w-100" alt="Cinema production lighting">
						</div>
					</div>
				</div>
			</div>
		</div>
	`;
};

const renderHomeFallbackMessage = () => {
	const hero = document.querySelector('[data-home-hero]');
	if (hero) {
		hero.insertAdjacentHTML('afterbegin', '<div class="alert alert-warning mb-4">Live homepage data could not be loaded right now.</div>');
	}
};