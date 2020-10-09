function apiModifyTableGroup(originalData,id,response){
    angular.forEach(originalData, function (item,key) {
        if(item.GROUP_ID == id){
            originalData[key] = response;
        }
    });
    return originalData;
}

function apiModifyTableEmployee(originalData,id,response){
    angular.forEach(originalData, function (item,key) {
        if(item.EMPLOYEE_NIK == id){
            originalData[key] = response;
        }
    });
    return originalData;
}

function apiModifyTableDivision(originalData,id,response){
    angular.forEach(originalData, function (item,key) {
        if(item.DIVISION_ID == id){
            originalData[key] = response;
        }
    });
    return originalData;
}

function apiModifyTablePosition(originalData,id,response){
    angular.forEach(originalData, function (item,key) {
        if(item.POSITION_ID == id){
            originalData[key] = response;
        }
    });
    return originalData;
}

function apiModifyTableUser(originalData,id,response){
    angular.forEach(originalData, function (item,key) {
        if(item.id == id){
            originalData[key] = response;
        }
    });
    return originalData;
}