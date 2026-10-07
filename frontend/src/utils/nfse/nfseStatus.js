// Situação da NFS-e (coluna nfses.status) → rótulo e cor do badge
const NFSE_STATUS_BADGES = {
  pending: { label: "processando", class: "badge-warning" },
  authorized: { label: "emitida", class: "badge-success" },
  rejected: { label: "rejeitada", class: "badge-error" },
  cancelled: { label: "cancelada", class: "badge-neutral" },
};

export function nfseStatusBadge(status) {
  return NFSE_STATUS_BADGES[status] || { label: status, class: "badge-ghost" };
}
