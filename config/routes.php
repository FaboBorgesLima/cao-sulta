<?php

use App\Controller\AuthenticationController;
use App\Controller\StaticPagesController;
use App\Controller\UserController;
use Core\Router\Route;

Route::get("/", [StaticPagesController::class, "home"])->name("home");
Route::get("/create", [UserController::class, "create"])->name("user.create");
Route::post("/create", [UserController::class, "store"])->name("user.store");
Route::get("/auth", [AuthenticationController::class, "new"])->name("auth.new");
Route::get("/auth/user/{id}/{cpf}", [AuthenticationController::class, "send"])->name("auth.send");
Route::get("/auth/{token}", [AuthenticationController::class, "authenticate"])->name("auth.auth");
Route::post("/auth/user", [AuthenticationController::class, "find"])->name("auth.find");

Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [UserController::class, "dashboard"])->name("dashboard");
    Route::get("/logout", [AuthenticationController::class, "logout"])->name("auth.logout");
});
