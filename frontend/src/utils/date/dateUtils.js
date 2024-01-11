
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

export function formatDateTimeBr(dateTime) {
    // Verifica se a data é válida
    if (!dateTime) return "";

    const dateObj = new Date(dateTime);
    const day = dateObj.getDate();
    const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
    const year = dateObj.getFullYear();
    const hours = padZero(dateObj.getHours());
    const minutes = padZero(dateObj.getMinutes());
    const seconds = padZero(dateObj.getSeconds());

    // Formate a data no formato desejado (exemplo: dd/mm/aaaa hh:mm:ss)
    const dateTimeBr = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

    return dateTimeBr;
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

function padZero(number) {
    return number < 10 ? `0${number}` : number;
}