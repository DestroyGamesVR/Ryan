(function () {
    const input = document.getElementById("passwordInput");
    const message = document.getElementById("message");
    const notice = document.querySelector(".downloadnotice");

    /*
      The live SMLWiki sent the typed phrase to:
      /laptop/terus-frontend/handleE.php
      That server-side PHP is not present in a static archive, so the request
      can never return the phrase.

      Put recovered codes in this table. MARVIN is included from the archive
      behavior you identified.
    */
    const VALID_CODES = {
        marvin: "marvin"
    };

    input.addEventListener("input", function () {
        this.value = this.value.replace(/[^a-zA-Z]/g, "").toUpperCase();

        if (this.value.length === 6) {
            displayMessage();
        } else {
            hideDownload();
        }
    });

    input.addEventListener("keydown", function (event) {
        if (
            event.key.length === 1 &&
            !/[a-zA-Z]/.test(event.key)
        ) {
            event.preventDefault();
        }
    });

    function safePlay(url) {
        try {
            const a = new Audio(url);
            const p = a.play();
            if (p && p.catch) p.catch(function () {});
        } catch (_) {}
    }

    function displayMessage() {
        const inputText = input.value.toLowerCase();
        const phrase = VALID_CODES[inputText];

        if (phrase) {
            handlePhrase(phrase);
        } else {
            showInvalid();
        }
    }

    function showInvalid() {
        hideDownload();
        message.style.visibility = "visible";
        message.style.opacity = "1";
        message.style.animation = "none";
        void message.offsetWidth;
        safePlay("https://nez.fyi/sml-assets/wrong.ogg");
        setTimeout(function () {
            message.style.animation = "fadeOut 3s ease forwards";
        }, 100);
    }

    function hideDownload() {
        if (!notice) return;
        notice.style.visibility = "hidden";
        notice.style.transform = "translateY(-30px)";
        notice.style.cursor = "default";
        notice.onclick = null;
        notice.innerText = "🗅 Downloading...";
    }

    function handlePhrase(phrase) {
        message.style.visibility = "hidden";
        safePlay("https://nez.fyi/sml-assets/pianos01.ogg");
        preload3dgameassets();

        notice.innerText = "🗅 Downloading...";
        notice.style.visibility = "visible";
        notice.style.transform = "translateY(-8px)";
        notice.style.cursor = "default";
        notice.onclick = null;

        setTimeout(function () {
            safePlay("https://nez.fyi/sml-assets/pianos02.ogg");
            notice.innerText = "🗈 - " + phrase;
            notice.style.cursor = "pointer";

            // Remember the fake-PC download locally, since the original
            // server-side session/database is unavailable in an archive.
            try {
                const downloads = JSON.parse(localStorage.getItem("smlwiki_fakepc_downloads") || "[]");
                if (!downloads.includes(phrase)) downloads.push(phrase);
                localStorage.setItem("smlwiki_fakepc_downloads", JSON.stringify(downloads));
                localStorage.setItem("smlwiki_download_" + phrase, "true");
            } catch (_) {}

            notice.onclick = function () {
                openArchivedDownload(phrase);
            };
        }, 4230);
    }

    async function openArchivedDownload(phrase) {
        // Static archives commonly save PHP pages as either .html or .php.
        const candidates = [
            "./cd/" + phrase + ".html",
            "./cd/" + phrase + ".php",
            "./cd/" + phrase + "/index.html"
        ];

        for (const url of candidates) {
            try {
                const response = await fetch(url, { method: "GET", cache: "no-store" });
                if (response.ok) {
                    window.location.href = url;
                    return;
                }
            } catch (_) {}
        }

        // The code itself still "downloads" to the fake PC even if the
        // archived CD page is missing.
        notice.innerText = "🗈 - " + phrase + " (downloaded)";
        message.textContent = phrase + " downloaded";
        message.style.visibility = "visible";
        message.style.opacity = "1";
        message.style.animation = "none";
        void message.offsetWidth;
        setTimeout(function () {
            message.style.animation = "fadeOut 3s ease forwards";
        }, 100);
    }

    const gameimagelist = [
        "aeth1.png","aeth2.png","aeth3.png","aeth4.png","trash2.png","trash1.png",
        "carpet.jpeg","creamwall.jpeg","jimmy.png","wood_floor.jpg","roof.jpeg",
        "laptop-bottom.jpeg","laptop-bottomside.jpeg","laptop-top.jpeg","laptop-side.jpeg",
        "mudcracked.jpeg","meatlot.jpeg","spread.jpeg","skytest.jpeg","paintflor.jpeg",
        "smogsclouds.png","woodtexture1.jpg","floorvirus.png","scratches.png","wallvirus.png",
        "trees.png","house01.png","house02.png","house03.png","house04.png","house05.png",
        "cieling.jpeg","carpet.jpeg","trashguts.png","trashguts2.png","trashguts3.png",
        "wind_broke.png","wind.png"
    ];

    function preload3dgameassets() {
        for (let i = 0; i < gameimagelist.length; i++) {
            const img = new Image();
            // Relative path works on localhost no matter which folder the archive is hosted under.
            img.src = "./texture/" + gameimagelist[i];
        }
    }
})();
