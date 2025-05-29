import Axios from "axios";

const baseURL = import.meta.env.VITE_APP_URL
const csrfToken = document.querySelector('[name=crsf-token]')?.getAttribute('content');

export const axios = Axios.create({
    baseURL,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-XSRF-Token': csrfToken,
    },
});

axios.defaults.withCredentials = true;

export const getUserInfo = async () => {
    const response = await axios.get(`user`);
    return response.data;
}

export const logout = async () => {
    return axios.post(`logout`);
}

export const getSurveys = async () => {
    const response = await axios.get('api/surveys');
    return response.data;
}

export const createSurvey = async (data) => {
    const response = await axios.post('api/surveys', data);
    return response.data;
}

export const getSurvey = async (id) => {
    const response = await axios.get(`api/surveys/${id}`);
    return response.data;
}

export const updateSurvey = async (id, data) => {
    const response = await axios.put(`api/surveys/${id}`, data);
    return response.data;
}

export const deleteSurvey = async (id) => {
    const response = await axios.delete(`api/surveys/${id}`);
    return response.data;
}

export const createSurveyField = async (surveyId, data) => {
    const response = await axios.post(`api/surveys/${surveyId}/fields`, data);
    return response.data;
}

export const deleteSurveyField = async (surveyId, fieldId) => {
    const response = await axios.delete(`api/surveys/${surveyId}/fields/${fieldId}`);
    return response.data;
}

export const getSurveyResponses = async (surveyId) => {
    const response = await axios.get(`api/surveys/${surveyId}/responses`);
    return response.data;
}

export const getSurveyResponseDetails = async (surveyId, responseId) => {
    const response = await axios.get(`api/surveys/${surveyId}/responses/${responseId}`);
    return response.data;
}

export const getSurveySummaries = async (surveyId) => {
    const response = await axios.get(`api/surveys/${surveyId}/summaries`);
    return response.data;
}

export const createSurveySummary = async (surveyId) => {
    const response = await axios.post(`api/surveys/${surveyId}/summaries`);
    return response.data;
}

export const getSurveySummary = async (surveyId, summaryId) => {
    const response = await axios.get(`api/surveys/${surveyId}/summaries/${summaryId}`);
    return response.data;
}

export const getSurveyForParticipant = async (id) => {
    const response = await axios.get(`api/participate/${id}`);
    return response.data;
}

export const submitSurveyResponse = async (id, data) => {
    const response = await axios.post(`api/participate/${id}`, data);
    return response.data;
}
