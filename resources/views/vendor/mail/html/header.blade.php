@props(['url'])

<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img
                src="{{ asset('images/company-logo.png') }}"
                width="120"
                alt="RAPID Logo"
                style="
                    display: block;
                    width: 120px;
                    max-width: 120px;
                    height: auto;
                    border: 0;
                "
            >
        </a>
    </td>
</tr>