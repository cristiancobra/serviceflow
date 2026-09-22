// Gerencia a aplicação do tema (claro/escuro/automático) definido em Conta.
const THEME_NAMES = {
  light: 'service-light',
  dark: 'service-dark',
};

const NIGHT_START_HOUR = 18; // 18h
const NIGHT_END_HOUR = 6; // 6h

function isNightTime(date = new Date()) {
  const hour = date.getHours();
  return hour >= NIGHT_START_HOUR || hour < NIGHT_END_HOUR;
}

export function resolveThemeName(preference) {
  if (preference === 'light') return THEME_NAMES.light;
  if (preference === 'dark') return THEME_NAMES.dark;
  return isNightTime() ? THEME_NAMES.dark : THEME_NAMES.light;
}

export function applyTheme(preference) {
  document.documentElement.setAttribute('data-theme', resolveThemeName(preference));
}

let autoRefreshInterval = null;

// Reaplica o tema periodicamente para o modo "automático" trocar sozinho às 18h/6h
// enquanto a aplicação permanece aberta.
export function startThemeAutoRefresh(getPreference) {
  if (autoRefreshInterval) return;

  autoRefreshInterval = setInterval(() => {
    applyTheme(getPreference());
  }, 60000);
}
