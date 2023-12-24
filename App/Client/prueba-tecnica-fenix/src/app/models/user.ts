export interface User {
    id_user:number,
    email:string,
    password?:string,
    first_name:string,
    last_name:string,
    type_document:number,
    document:string,
    phone:string,
    address:string,
    birthdate:string,
    is_admin:boolean,
}