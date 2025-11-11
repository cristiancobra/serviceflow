const colors = [
    "text-green-600",
    "text-blue-600", 
    "text-red-600",
    "text-orange-600",
    "text-purple-600",
    "text-indigo-600",
    "text-pink-600",
    "text-teal-600",
    "text-emerald-600",
    "text-cyan-600",
    "text-violet-600",
    "text-fuchsia-600",
    "text-rose-600",
    "text-lime-600",
    "text-amber-600",
    "text-sky-600",
    "text-slate-600",
    "text-green-500",
    "text-blue-500",
    "text-red-500",
    "text-orange-500",
    "text-purple-500",
    "text-indigo-500",
    "text-pink-500",
    "text-teal-500",
    "text-emerald-500",
    "text-cyan-500",
    "text-violet-500",
    "text-fuchsia-500",
    "text-rose-500",
    "text-lime-500",
    "text-amber-500",
    "text-sky-500",
];

export function getColorClassForName(name) {
    const hash = Array.from(name).reduce((acc, char) => (acc + char.charCodeAt(0)), 0);
    return colors[hash % colors.length];
}

export function getStatusColor(date) {

    if (date) {
        return "done";
    }
    return "canceled";
}

export function getStatusClass(status) {

    switch (status) {
        case "canceled":
            return "canceled";
        case "doing":
            return "doing";
        case "done":
            return "done";
        case "to-do":
            return "to-do";
        case "wait":
            return "wait";
        default:
            return "sem-status";
    }
}

export function getDeadlineClass(dateDue) {
    const today = new Date();
    const formatedDateDue = new Date(dateDue + "T00:00:00");

    // Normalizar as datas para ignorar o horário
    today.setHours(0, 0, 0, 0);
    formatedDateDue.setHours(0, 0, 0, 0);

    // Comparar apenas ano, mês e dia
    if (
        formatedDateDue.getFullYear() === today.getFullYear() &&
        formatedDateDue.getMonth() === today.getMonth() &&
        formatedDateDue.getDate() === today.getDate()
    ) {
        return "featured-text";
    }

    if (formatedDateDue < today) {
        return "alert-text";
    }

    return "default-text";
}

export function getPriorityClass(priority) {

    switch (priority) {
        case "high":
            return "high";
        case "medium":
            return "medium";
        case "low":
            return "low";
    }
}

export function getStatusIcon(date) {


    if (date) {
        return "fas fa-check-circle";
    }
    return "fas fa-stop";

}

export function trimName(description) {
    if (description) {
        return description.substring(0, 50);
    }
}