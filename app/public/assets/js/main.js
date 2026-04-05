document.addEventListener('DOMContentLoaded', () => {
	initGigScroller();
	initSignupRoleSwitching();
});

const initGigScroller = () => {
	const scrollContainer = document.querySelector('[data-gigs-scroll-container]');
	const scrollButtons = document.querySelectorAll('[data-gigs-action]');

	if (!scrollContainer || scrollButtons.length === 0) {
		return;
	}

	const getScrollAmount = () => {
		const firstCard = scrollContainer.querySelector('.gig-scroll-card');

		if (!firstCard) {
			return 320;
		}

		const computedStyle = window.getComputedStyle(scrollContainer.querySelector('.d-flex') ?? firstCard);
		const gapValue = parseFloat(computedStyle.columnGap || computedStyle.gap || '0') || 0;

		return firstCard.getBoundingClientRect().width + gapValue;
	};

	const scrollByAmount = (direction) => {
		scrollContainer.scrollBy({
			left: direction * getScrollAmount(),
			behavior: 'smooth',
		});
	};

	let autoScrollTimer = null;

	const startAutoScroll = () => {
		stopAutoScroll();
		autoScrollTimer = window.setInterval(() => {
			const maxScrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth;

			if (scrollContainer.scrollLeft >= maxScrollLeft - 8) {
				scrollContainer.scrollTo({ left: 0, behavior: 'smooth' });
				return;
			}

			scrollByAmount(1);
		}, 3500);
	};

	const stopAutoScroll = () => {
		if (autoScrollTimer !== null) {
			window.clearInterval(autoScrollTimer);
			autoScrollTimer = null;
		}
	};

	const restartAutoScroll = () => {
		startAutoScroll();
	};

	scrollButtons.forEach((button) => {
		button.addEventListener('click', () => {
			const direction = button.dataset.gigsAction === 'prev' ? -1 : 1;
			scrollByAmount(direction);
			restartAutoScroll();
		});
	});

	scrollContainer.addEventListener('mouseenter', stopAutoScroll);
	scrollContainer.addEventListener('mouseleave', startAutoScroll);
	scrollContainer.addEventListener('focusin', stopAutoScroll);
	scrollContainer.addEventListener('focusout', startAutoScroll);

	startAutoScroll();
};

const initSignupRoleSwitching = () => {
	const roleSelect = document.querySelector('[data-signup-role-select]');

	if (!roleSelect) {
		return;
	}

	const roleSections = document.querySelectorAll('[data-signup-role-section]');
	if (roleSections.length === 0) {
		return;
	}

	const syncRoleFields = () => {
		const activeRole = roleSelect.value;

		roleSections.forEach((section) => {
			const isVisible = section.dataset.signupRoleSection === activeRole;
			section.classList.toggle('d-none', !isVisible);

			section.querySelectorAll('[data-signup-required]').forEach((field) => {
				field.required = isVisible;
			});
		});
	};

	roleSelect.addEventListener('change', syncRoleFields);
	syncRoleFields();
};
