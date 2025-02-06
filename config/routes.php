<?php

use App\Controller\AuthenticationController;
use App\Controller\CRMVRegisterController;
use App\Controller\PetController;
use App\Controller\StaticPagesController;
use App\Controller\UserController;
use App\Controller\VetController;
use Core\Router\Route;

Route::get("/", [StaticPagesController::class, "home"])->name("home");
Route::get("/register", [StaticPagesController::class, "register"])->name("register");

// ---------- User
Route::get("/user/create", [UserController::class, "create"])->name("user.create");
Route::get("/user/{id}", [UserController::class, "show"])->name("user.show");
Route::post("/user/store", [UserController::class, "store"])->name("user.store");

// ---------- Auth
Route::get("/auth", [AuthenticationController::class, "login"])->name("auth.login");
Route::get("/auth/user/{id}/{cpf}", [AuthenticationController::class, "send"])->name("auth.send");
Route::get("/auth/{token}", [AuthenticationController::class, "authenticate"])->name("auth.auth");
Route::post("/auth/user", [AuthenticationController::class, "find"])->name("auth.find");

// ---------- Vet

Route::get("/vet/create", [VetController::class, "create"])->name("vet.create");
Route::post("/vet/store", [VetController::class, "store"])->name("vet.store");

// ---------- CRMV Register

Route::get("/vet/{id}/crmv-register", [CRMVRegisterController::class, "show"])->name("crmv-registers.show");

// ---------- Pet
Route::get("/user/{id}/pets", [PetController::class, "index"])->name("pet.index");

Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [UserController::class, "dashboard"])->name("dashboard");

    // ---------- Pet
    Route::get("/pet/create", [PetController::class, "create"])->name("pet.create");
    Route::post("/pet/store", [PetController::class, "store"])->name("pet.store");
    Route::get("/pet/{id}/update", [PetController::class, "update"])->name("pet.update");
    Route::post("/pet/{id}/save", [PetController::class, "save"])->name("pet.save");
    Route::get("/pet/{id}/delete", [PetController::class, "delete"])->name("pet.delete");


    // ---------- Auth
    Route::get("/logout", [AuthenticationController::class, "logout"])->name("auth.logout");
});
