const userAcceptPermisionElements = document.getElementsByClassName(
  "user-accept-permission"
);

for (const userAcceptPermisionEl of userAcceptPermisionElements) {
  userAcceptPermisionEl.addEventListener("click", (ev) => {
    if (ev.target instanceof HTMLElement) {
      ev.target.dataset["vet"];
      ev.target.dataset["user"];
    }
  });
}
