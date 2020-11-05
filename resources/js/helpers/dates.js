import moment from "moment";

export const getDateInDayMonthFormat = date => moment(date).format('DD/MM');
