async function postForm(url, data = {}) {
  const body = new FormData();
  Object.entries(data).forEach(([k, v]) => {
    if (v !== undefined && v !== null) body.append(k, v);
  });
  const res = await fetch(url, { method: "POST", body });
  const json = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(json.detail || json.error || res.statusText);
  return json;
}

function flash(el, msg, err = false) {
  if (!el) return alert(msg);
  el.textContent = typeof msg === "string" ? msg : JSON.stringify(msg);
  el.className = "flash" + (err ? " err" : "");
}

document.addEventListener("click", async (e) => {
  const btn = e.target.closest("[data-action]");
  if (!btn) return;
  e.preventDefault();
  const action = btn.dataset.action;
  const status = document.getElementById("status");
  btn.disabled = true;
  try {
    if (action === "approve") {
      const id = btn.dataset.id;
      const body = document.getElementById("body-" + id)?.value;
      const subject = document.getElementById("subject-" + id)?.value;
      const result = await postForm("/api/approvals/" + id + "/approve", { body, subject });
      flash(status, result.sent ? "Sent." : "Approved — it will show on Today for you to send.");
      btn.closest(".card")?.remove();
    } else if (action === "reject") {
      await postForm("/api/approvals/" + btn.dataset.id + "/reject", { note: "rejected in UI" });
      btn.closest(".card")?.remove();
    } else if (action === "mark-sent") {
      await postForm("/api/today/" + btn.dataset.id + "/mark-sent");
      btn.closest(".card")?.remove();
    } else if (action === "copy") {
      const text = document.getElementById("copy-" + btn.dataset.id)?.value || "";
      await navigator.clipboard.writeText(text);
      flash(status, "Copied.");
    } else if (action === "enrich") {
      const result = await postForm("/api/hunter/enrich/" + btn.dataset.id);
      flash(status, "Enriched. Score " + result.score);
      location.reload();
    } else if (action === "draft") {
      const result = await postForm("/api/copywriter/draft-prospect/" + btn.dataset.id);
      flash(status, "Drafted " + result.created + " messages. Check Approvals.");
    } else if (action === "tick") {
      flash(status, await postForm("/api/sequencer/tick"));
    } else if (action === "imap") {
      flash(status, await postForm("/api/imap/poll"));
    } else if (action === "enrich-all") {
      flash(status, await postForm("/api/hunter/enrich-all"));
    }
  } catch (err) {
    flash(status, err.message, true);
  } finally {
    btn.disabled = false;
  }
});
