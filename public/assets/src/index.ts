const userAcceptPermisionElements = document.getElementsByClassName(
  "user-accept-permission"
);
interface AcceptPermission {
  vet: string;
  user: string;
}

for (const userAcceptPermisionEl of userAcceptPermisionElements) {
  userAcceptPermisionEl.addEventListener("click", (ev) => {
    if (!(ev.target instanceof HTMLElement)) {
      return;
    }

    const data = {
      vet: ev.target.dataset["vet"] || "",
      user: ev.target.dataset["user"] || "",
    };

    acceptPermission(data).then((data) => {
      alert(data.accepted ? "accepted" : "an error has ocurred");
      if (!(ev.target instanceof HTMLElement)) {
        return;
      }
      ev.target.style.display = "none";
    });
  });
}

async function acceptPermission(
  data: AcceptPermission
): Promise<{ accepted: boolean }> {
  const request = new Request(
    `http://127.0.0.1/vet/${data.vet}/user/${data.user}/permission`,
    {
      method: "PUT",
      body: JSON.stringify({ accepted: true }),
    }
  );

  return request.json();
}
