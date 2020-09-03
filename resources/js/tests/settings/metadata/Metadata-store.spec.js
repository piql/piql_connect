import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from "vuex";
import template from "@/store/metadata/template.js";
import { cloneDeep } from 'lodash';

const templatesArrayFixture = [
    {"id": "3bdcce12-4fc8-4c07-bada-e1418bff9465", "created_at": "2020-05-22", "metadata":{"dc":{"title":"Svalbard","creator":"a photographer","subject":"cold things","description":"brrr","publisher":"","contributor":"","date":"","type":"","format":"","identifier":"63ff83ef-862e-428f-9cc3-2040693d493e","source":"","language":"","relation":"","coverage":"","rights":""}}},
    {"id": "870c6012-506f-4e47-8fbd-54c8b0dea6c0", "created_at": "2020-05-23", "metadata":{"dc":{"title":"Drammen","creator":"that guy", "subject": "awesome place", "description": "yay", "publisher": "", "contributor": "", "date": "", "type": "", "format": "", "identifier": "63ff83ef-862e-428f-9cc3-2040693d493e", "source": "", "language": "", "relation": "", "coverage": "", "rights": "" } } }
];


describe("Template store getters", ()=> {
    const localVue = createLocalVue();
    localVue.use(Vuex);
    const templateStore = new Vuex.Store( cloneDeep( template ) );

    templateStore.state.templates = templatesArrayFixture;

    it("returns the list of templates", ()=> {
        expect( (templateStore.getters.templates.length) ).toBe(2);
    });

    it("returns templates with the expected ids for templates", () => {
        expect( (templateStore.getters.templates[0].id) ).toBe("3bdcce12-4fc8-4c07-bada-e1418bff9465");
        expect( (templateStore.getters.templates[1].id) ).toBe("870c6012-506f-4e47-8fbd-54c8b0dea6c0");
    });

    it("returns the correct template for templateById", () => {
        expect( (templateStore.getters.templateById("870c6012-506f-4e47-8fbd-54c8b0dea6c0").created_at)).toBe("2020-05-23")
        expect( (templateStore.getters.templateById("3bdcce12-4fc8-4c07-bada-e1418bff9465").created_at)).toBe("2020-05-22")
    });
});

describe("Template store actions", () => {
    const localVue = createLocalVue();
    localVue.use(Vuex);
    const templateStore = new Vuex.Store( cloneDeep( template ) );

    templateStore.dispatch('fetchTemplates');

    let beforeAddCount = templateStore.getters.templates.length;

    const newTemplate = { "metadata":{"dc":
        {"title":"Drammen","creator":"that guy", "subject": "awesome place", "description": "yay", "publisher": "", "contributor": "",
            "date": "", "type": "", "format": "", "identifier": "63ff83ef-862e-428f-9cc3-2040693d493e", "source": "", "language": "",
            "relation": "", "coverage": "", "rights": "" } } };

    it("can add templates", () => {
        let result = templateStore.dispatch('addTemplate', newTemplate );
        expect( templateStore.getters.templates.length ).toBe(beforeAddCount+1);
    });

});

