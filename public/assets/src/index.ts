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
    });
  });
}

const userDeletePermisionElements = document.getElementsByClassName(
  "user-deny-permission"
);
interface AcceptPermission {
  vet: string;
  user: string;
}

for (const userDeletePermisionEl of userDeletePermisionElements) {
  userDeletePermisionEl.addEventListener("click", (ev) => {
    if (!(ev.target instanceof HTMLElement)) {
      return;
    }

    const data = {
      vet: ev.target.dataset["vet"] || "",
      user: ev.target.dataset["user"] || "",
    };
    const del = confirm(
      "the action of deleting this permission is permanent, do you want to continue?"
    );

    if (!del) {
      return;
    }

    deletePermission(data).then((data) => {
      if (!(ev.target instanceof HTMLElement)) {
        return;
      }

      if (ev.target.parentElement) {
        if (ev.target.parentElement.parentElement) {
          ev.target.parentElement.parentElement.style.display = "none";
        }
      }

      alert(data.destroyed ? "deleted" : "an error has ocurred");
      if (!(ev.target instanceof HTMLElement)) {
        return;
      }
    });
  });
}

async function acceptPermission(
  data: AcceptPermission
): Promise<{ accepted: boolean }> {
  const request = fetch(
    `http://127.0.0.1/vet/${data.vet}/user/${data.user}/permission`,
    {
      method: "PUT",
      body: JSON.stringify({ accepted: true }),
      headers: {
        "Content-type": "application/json; charset=UTF-8",
      },
    }
  );

  return (await request).json();
}

async function deletePermission(data: AcceptPermission): Promise<any> {
  const request = fetch(`/vet/${data.vet}/user/${data.user}/permission`, {
    method: "DELETE",
    headers: {
      "Content-type": "application/json; charset=UTF-8",
    },
  });

  return (await request).json();
}
