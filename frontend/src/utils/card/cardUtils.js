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

export function getStatusIcon(status) {

    switch (status) {
        case "to-do":
            return "fas fa-calendar"; // Ícone para "fazendo"
        case "doing":
            return "fas fa-clock"; // Ícone para "fazendo"
        case "done":
            return "fas fa-check-circle"; // Ícone para "feito"
        case "wait":
            return "fas fa-pause-circle"; // Ícone para "esperando"
        default:
            return "fas fa-question-circle"; // Ícone padrão para outros status desconhecidos
    }
}