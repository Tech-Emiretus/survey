import { createWebHistory, createRouter } from 'vue-router'

import Surveys from './pages/Surveys/Index.vue'
import SurveyAddEdit from './pages/Surveys/AddEdit.vue'
import SurveyDetails from './pages/Surveys/Show.vue'
import SurveyResponse from './pages/SurveyResponses/Show.vue'
import SurveySummary from './pages/SurveySummaries/Show.vue'
import ParticipateSurvey from './pages/Participate/Index.vue'

const routes = [
    { path: '/', component: Surveys, name: 'surveys', meta: {name: 'Surveys' }},
    { path: '/dashboard', component: Surveys, name: 'surveys-dashboard', meta: {name: 'Surveys' }},
    { path: '/surveys', component: Surveys, name: 'surveys-home', meta: {name: 'Surveys' }},
    { path: '/surveys/create', component: SurveyAddEdit, name: 'create-survey', meta: {name: 'Add New Survey' }},
    { path: '/surveys/edit/:id', component: SurveyAddEdit, name: 'edit-survey', meta: {name: 'Edit Survey' }},
    { path: '/surveys/:id', component: SurveyDetails, name: 'view-survey', meta: {name: 'View Survey' }},
    { path: '/surveys/:surveyId/responses/:responseId', component: SurveyResponse, name: 'view-survey-response', meta: {name: 'View Survey Response' }},
    { path: '/surveys/:surveyId/summaries/:summaryId', component: SurveySummary, name: 'view-survey-summary', meta: {name: 'View Survey Summary' }},
    { path: '/participate/:id', component: ParticipateSurvey, name: 'participate-survey', meta: {name: 'Participate in Survey' }},
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
