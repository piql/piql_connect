const utils = {        
    formatDate: (ISOdate)=>{
        let date = new Date(ISOdate);
        let year = date.getFullYear();
        let month = date.getMonth()+1;
        let dt = date.getDate();
        let time = date.getHours() + ':'+ date.getMinutes();

        if (dt < 10) {
        dt = '0' + dt;
        }
        if (month < 10) {
        month = '0' + month;
        }

        return year+'-' + month + '-'+dt + ' '+time;
        
    }
}

module.exports = utils;