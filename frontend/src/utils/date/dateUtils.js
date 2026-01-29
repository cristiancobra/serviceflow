// Método para converter uma data UTC para o fuso horário local do usuário
// export function convertUtcToLocal(utcDateString) {
//     if (!utcDateString) return '';
//   console.log(utcDateString);
//     const utcDate = new Date(utcDateString);
//     console.log(utcDate);
//     return utcDate.toLocaleString();
//   }

export function convertDecimalToSeconds(decimalHours) {
    const seconds = decimalHours * 3600;
    return Math.round(seconds);
}

export function convertSecondsToDecimal(seconds) {
    const hours = seconds / 3600;
    return parseFloat(hours.toFixed(1));
}

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

    // Formatar a data e hora sem segundos
    const formattedDate = `${day}/${month}/${year} ${hours}:${minutes < 10 ? '0' : ''}${minutes}`;

    return formattedDate;
}

export function convertUtcTimeToLocal(utcDateString) {
    if (!utcDateString) return '--:--';

    const utcDate = new Date(utcDateString);

    const localDate = new Date(utcDate.getTime() + (utcDate.getTimezoneOffset() * 60000));

    const hours = localDate.getHours();
    const minutes = localDate.getMinutes();

    var formattedTime = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;

    return formattedTime;
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

    // Converter de UTC para timezone local
    const dateObj = new Date(date);
    const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
    const localDate = new Date(dateObj.getTime() + userTimezoneOffset);

    const padZero = (num) => num.toString().padStart(2, '0');

    const day = padZero(localDate.getDate());
    const month = padZero(localDate.getMonth() + 1);
    const year = localDate.getFullYear();

    // Formate a data no formato desejado (exemplo: dd/mm/aaaa)
    const dateBr = `${day}/${month}/${year}`;

    return dateBr;

}

// export function formatDateTimeBr(dateTime) {
//     // Verifica se a data é válida
//     if (!dateTime) return "";

//     const dateObj = new Date(dateTime);
//     const day = dateObj.getDate();
//     const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
//     const year = dateObj.getFullYear();
//     const hours = padZero(dateObj.getHours());
//     const minutes = padZero(dateObj.getMinutes());
//     const seconds = padZero(dateObj.getSeconds());

//     // Formate a data no formato desejado (exemplo: dd/mm/aaaa hh:mm:ss)
//     const dateTimeBr = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

//     return dateTimeBr;
// }

// NÃO USAR MAIS - timezone salvo no banco
// export function convertDateTimeForServer(dateTimeString) {
//     console.log("Converting dateTimeString for server:", dateTimeString);
//     const d = new Date(dateTimeString);
  
//     const pad = n => String(n).padStart(2, "0");
  
//     return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ` +
//            `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
//   }

export function convertDateTimeToLocal(datetime) {
    const dateObj = new Date(datetime);
    const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
    // Adicionar o offset (não subtrair) para converter UTC para local
    const localDate = new Date(dateObj.getTime() + userTimezoneOffset);

    const padZero = (num) => num.toString().padStart(2, '0');

    const day = padZero(localDate.getDate());
    const month = padZero(localDate.getMonth() + 1);
    const year = localDate.getFullYear();
    const hours = padZero(localDate.getHours());
    const minutes = padZero(localDate.getMinutes());
    const seconds = padZero(localDate.getSeconds());

    const dateTimeLocal = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    return dateTimeLocal;
}

export function convertDateToLocal(dateString) {
    if (!dateString) return '';
    
    // Para datas puras, apenas retornar como está (YYYY-MM-DD)
    return dateString.toString();
}

export function convertDateForServer(dateString) {
    if (!dateString) return null;
    
    // Para datas puras, apenas retornar como está (YYYY-MM-DD)
    return dateString.toString();
}

export function displayDate(datetimeLocal) {
    if (datetimeLocal === null || datetimeLocal === undefined) return 'sem data';

    // Se for uma string no formato YYYY-MM-DD, parsear sem criar Date
    if (typeof datetimeLocal === 'string' && datetimeLocal.match(/^\d{4}-\d{2}-\d{2}$/)) {
        const parts = datetimeLocal.split('-');
        const year = parts[0];
        const month = parts[1];
        const day = parts[2];
        return `${day}/${month}/${year}`;
    }

    const dateObj = new Date(datetimeLocal);

    const padZero = (num) => num.toString().padStart(2, '0');

    const day = padZero(dateObj.getDate());
    const month = padZero(dateObj.getMonth() + 1); // Os meses em JavaScript começam em 0, então adicionamos 1
    const year = dateObj.getFullYear();

    const date = `${day}/${month}/${year}`;

    return date;
}

export function displayTime(datetimeLocal) {
    if (!datetimeLocal) return '--:--';

    const dateObj = new Date(datetimeLocal);

    const padZero = (num) => num.toString().padStart(2, '0');
    
    const hours = padZero(dateObj.getHours());
    const minutes = padZero(dateObj.getMinutes());

    const time = `${hours}:${minutes}`;

    return time;
}

// export function formatTimeToLocal(datetime) {
//     const dateObj = new Date(datetime);
//     const userTimezoneOffset = dateObj.getTimezoneOffset() * 60000;
//     const localDate = new Date(dateObj.getTime() + userTimezoneOffset);

//     const hours = localDate.getHours();
//     const minutes = localDate.getMinutes();

//     return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
// }

export function formatDuration(seconds) {

    if (seconds === null || seconds === undefined) {
        return '--:--';
    }

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}