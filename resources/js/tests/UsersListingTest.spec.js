import { mount } from "@vue/test-utils";
import Listing from "../views/settings/Listing/Listing";
import { it } from "date-fns/locale";

describe("Listing", ()=>{
    let wrapper = mount(Listing);

    let date = "2020-07-17T11:42:17.000000Z";
    
    it("format date", () => {
        expect(wrapper.vm.formatDate(date)).toBe('2020-07-17');
       
      });


})