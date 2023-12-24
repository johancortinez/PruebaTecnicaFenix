import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  
  //public apiUrl = 'http://localhost/Proyectos/';
  public apiUrl = '/Proyectos/PruebaTecnica/index.php';

  public user?:User;
  public token:string = "";

  constructor(private http:HttpClient) { }
  
  private sendRequest(params:any):Observable<any> {
     return this.http.post(this.apiUrl, params);

  }//Fin sendRequest(){...}

  login(email:string, password:string) {

    return this.sendRequest({
      method: "Login",
      email: email,
      password: password
    });

  }//Fin login(){...}

  listTypesDocuments() {

    return this.sendRequest({
      token: this.token,
      method: "ListTypesDocument",
    });

  }//Fin listTypesDocuments(){...}

  searchUser(value:string) {
    return this.sendRequest({
      token: this.token,
      method: "Search",
      value: value,
    });
  }//Fin deleteUser() {...}

  saveUser(user:User) {

    let params = {
      token: this.token,
      method: "Save",
      id_user: user.id_user,
      first_name: user.first_name,
      last_name: user.last_name,
      type_document: user.type_document,
      document: user.document,
      email: user.email,
      password: user.password,
      phone: user.phone,
      address: user.address,
      birthdate: user.birthdate,
      is_admin: user.is_admin,
    };

    return this.sendRequest(params);

  }//Fin saveUser() {...}

  deleteUser(id_user:number) {
    return this.sendRequest({
      token: this.token,
      method: "Delete",
      id_user: id_user
    });
  }//Fin deleteUser() {...}
  
}
