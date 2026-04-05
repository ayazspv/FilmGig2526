/** Bootstraps the shared UI behaviors once the DOM is ready. */
document.addEventListener('DOMContentLoaded', onMainReady);

/** Starts the main page helpers. */
const onMainReady = () => {
	initSignupRoleSwitching();
};

/** Initializes role-based signup sections and required fields. */
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
		updateRoleSectionVisibility(roleSections, activeRole);
	};

	roleSelect.addEventListener('change', syncRoleFields);
	syncRoleFields();
};

/** Shows the active signup section and disables required fields on hidden sections. */
const updateRoleSectionVisibility = (roleSections, activeRole) => {
	roleSections.forEach((section) => {
		const isVisible = section.dataset.signupRoleSection === activeRole;
		section.classList.toggle('d-none', !isVisible);
		setSectionRequiredState(section, isVisible);
	});
};

/** Updates the required state of inputs inside one signup section. */
const setSectionRequiredState = (section, isVisible) => {
	section.querySelectorAll('[data-signup-required]').forEach((field) => {
		field.required = isVisible;
	});
};
