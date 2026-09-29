const colors = [
    "text-success",
    "text-info", 
    "text-error",
    "text-warning",
    "text-purple-600",
    "text-indigo-600",
    "text-pink-600",
    "text-teal-600",
    "text-success",
    "text-cyan-600",
    "text-violet-600",
    "text-fuchsia-600",
    "text-error",
    "text-lime-600",
    "text-warning",
    "text-info",
    "text-base-content/70",
    "text-success",
    "text-info",
    "text-error",
    "text-warning",
    "text-purple-500",
    "text-indigo-500",
    "text-pink-500",
    "text-teal-500",
    "text-success",
    "text-cyan-500",
    "text-violet-500",
    "text-fuchsia-500",
    "text-error",
    "text-lime-500",
    "text-warning",
    "text-info",
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

export function getDeadlineClass(dateDue, dateConclusion = null) {
    if (dateConclusion) {
        return "text-success";
    }

    const today = new Date();
    const formatedDateDue = new Date(dateDue + "T00:00:00");

    today.setHours(0, 0, 0, 0);
    formatedDateDue.setHours(0, 0, 0, 0);

    if (formatedDateDue < today) {
        return "text-error";
    }

    return "text-base-content";
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