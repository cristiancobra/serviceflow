// Método para converter uma data UTC para o fuso horário local do usuário
// export function convertUtcToLocal(utcDateString) {
//     if (!utcDateString) return '';
//   console.log(utcDateString);
//     const utcDate = new Date(utcDateString);
//     console.log(utcDate);
//     return utcDate.toLocaleString();
//   }

export function convertUtcToLocal(utcDateString) {
    if (!utcDateString) return '--:--';

    const utcDate = new Date(utcDateString);

    // Converter para hora local
    const localDate = new Date(utcDate.getTime() + (utcDate.getTimezoneOffset() * 60000));

    // Obter as partes da data
    const day = localDate.getDate();
    const month = localDate.getMonth() + 1;
    const year = localDate.getFullYear();
    const hours = localDate.getHours();
    const minutes = localDate.getMinutes();

    // Formatar a data sem segundos
    const formattedDate = `${day}/${month}/${year} ${hours}:${minutes < 10 ? '0' : ''}${minutes}`;

    return formattedDate;
}

export function displayLocalTime(utcDate) {

    if(!utcDate) return '--:--';
    
    var utcDateObj = new Date(utcDate);
    var localDateObj = new Date(utcDateObj.getTime() - (utcDateObj.getTimezoneOffset() * 60000));
    var hours = localDateObj.getHours();
    var minutes = localDateObj.getMinutes();
    var formattedTime = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
    
    return formattedTime;
}

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

export function formatDateTimeForServer(dateTimeString) {
    const date = new Date(dateTimeString);

    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // adiciona zero à esquerda se necessário
    const day = date.getDate().toString().padStart(2, '0');
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

export function formatDateTimeToLocal(datetime) {
    if (!datetime) return '--:--';

    const dateObj = new Date(datetime);
    const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
    const localDate = new Date(dateObj.getTime() - userTimezoneOffset); // Subtraímos o offset para obter o horário local

    const padZero = (num) => num.toString().padStart(2, '0');

    const day = padZero(localDate.getDate());
    const month = padZero(localDate.getMonth() + 1); // Os meses em JavaScript começam em 0, então adicionamos 1
    const year = localDate.getFullYear();
    const hours = padZero(localDate.getHours());
    const minutes = padZero(localDate.getMinutes());
    const seconds = padZero(localDate.getSeconds());

    const dateTimeLocal = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    return dateTimeLocal;
}


export function formatTimeToLocal(datetime) {
    const dateObj = new Date(datetime);
    const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
    const localDate = new Date(dateObj.getTime() + userTimezoneOffset);

    const hours = localDate.getHours();
    const minutes = localDate.getMinutes();

    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

export function formatDuration(seconds) {

    if (seconds === null || seconds === undefined) {
        return '--:--';
    }

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}

function padZero(number) {
    return number < 10 ? `0${number}` : number;
}