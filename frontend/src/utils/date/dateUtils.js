
export function formatDateBr(date) {

    // Verifica se a data é válida
    if (!date) return "";

    const dateObj = new Date(date);
    const day = dateObj.getDate();
    const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
    const year = dateObj.getFullYear();

    // Formate a data no formato desejado (exemplo: dd/mm/aaaa)
    const dateBr = `${day}/${month}/${year}`;

    return dateBr;

}

export function formatTimeBr(datetime) {
    const dateObj = new Date(datetime);
    const hours = dateObj.getHours();
    const minutes = dateObj.getMinutes();

    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

export function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}