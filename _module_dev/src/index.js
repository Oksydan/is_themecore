import { partytownSnippet } from '@builder.io/partytown/integration';

const snippetContent = partytownSnippet();

document.addEventListener('DOMContentLoaded', () => {
    const script = document.createElement("script");

    script.textContent=snippetContent;
    document.body.append(script)
});
