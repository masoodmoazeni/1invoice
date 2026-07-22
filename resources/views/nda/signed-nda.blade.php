
@php
    $dmSans = base64_encode(file_get_contents(public_path('assets/fonts/dm-sans/DMSans-Regular.woff2')));
    $theSeasons = base64_encode(file_get_contents(public_path('assets/fonts/headings/TheSeasons-Bold.ttf')));
    $brittany = base64_encode(file_get_contents(public_path('assets/fonts/signature/BrittanySignature.ttf')));
@endphp


<html lang="en">
<head>
    <title>Signed Non-Disclosure Agreement</title>
    <style>
        {!! file_get_contents(public_path('assets/css/signed-nda-pdf.css')) !!}
    </style>

    <style>
        @font-face {
            font-family: 'DM Sans';
            src: url(data:font/ttf;base64,{{ $dmSans }}) format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'TheSeasons';
            src: url(data:font/ttf;base64,{{ $theSeasons }}) format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: 'BrittanySignature';
            src: url(data:font/ttf;base64,{{ $brittany }}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: #404040;
            background: #fff;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'TheSeasons', serif;
            font-weight: 700;
        }

        .signature {
            font-family: 'BrittanySignature', cursive;
        }

        .font-dm-sans {
            font-family: 'DM Sans', sans-serif;
            font-weight: 400;
            letter-spacing: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
    </style>
</head>

<body>
    <div class="container mx-auto w-full px-4 py-8 md:px-6">
        <div class="py-10 md:px-4">
            <div class="flex items-center justify-center gap-2">
                <img src="{{ public_path('assets/media/saloon/SSC Logo-long-NO-background-2 1.png') }}" alt="Salonspa Connection Logo">
            </div>
        </div>
        <div class="mx-auto w-full max-w-6xl pt-4 pb-10 md:px-4">
            <div class="mb-8 text-center">
                <h1 class="mb-2 font-bold text-neutral-900 text-lg leading-10 md:text-2xl">Non-Disclosure Agreement</h1>
                <p class="text-[16px] text-neutral-500 leading-5">Listing ID: {{ $listing['id'] }}</p>
            </div>
            <div class="mb-8 space-y-6 px-4">
                <p class="text-[16px] leading-7">This business requires a confidentiality agreement for all buyers.</p>
                <p class="inline-block text-[16px] leading-7">
                    This Non-Disclosure Agreement ("Agreement") is between Salonspa Connection ("Disclosing Party") and
                    <span class="mx-2 inline-flex h-8">
                        <input
                            class="w-full min-w-0 border border-neutral-300 py-1 text-sm outline-none transition-[color,box-shadow] selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:font-medium file:text-foreground file:text-xs disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-xs dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 inline-flex h-auto rounded-none border-x-0 border-t-0 border-b border-b-neutral-300 bg-transparent! px-0 pt-0 pb-0 text-center text-[16px]! shadow-none placeholder:text-neutral-400 focus-visible:ring-0"
                            data-slot="input" type="text" placeholder="Your name" value="{{ $listing['fullname'] }}">
                        </span> who shall be covered by said Agreement ("Receiving Party") for the purpose of preventing the
                    unauthorized
                    disclosure of Your Name Confidential Information as defined below. <span
                        class="mx-2 inline-flex h-8">
                        <input
                            class="w-full min-w-0 border border-neutral-300 py-1 text-sm outline-none transition-[color,box-shadow] selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:font-medium file:text-foreground file:text-xs disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-xs dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 inline-flex h-auto rounded-none border-x-0 border-t-0 border-b border-b-neutral-300 bg-transparent! px-0 pt-0 pb-0 text-center text-[16px]! shadow-none placeholder:text-neutral-400 focus-visible:ring-0"
                            data-slot="input" type="text" placeholder="Your name" value="{{ $listing['fullname'] }}">
                        </span>
                            may be referred to collectively as "Parties." "We", "us", and "our" refer to both Parties below and the
                    Parties' respective affiliates.
                </p>
            </div>
            <table class="mb-8 w-full border border-border border-collapse">
                <tr class="border-border border-b">
                    <td class="border-border border-r p-4 align-top">
                        <p class="flex flex-col text-[16px] leading-5">
                            <span class="font-medium">Company and Its Affiliates or Individual:</span>
                            <span class="mt-1">Inform.Guide.Hire LLC DBA Salonspa Connection</span>
                        </p>
                    </td>
                    <td class="border-border border-r p-4 align-top">
                        <p class="font-medium text-[16px] leading-5">Company and Its Affiliates or Individual:</p>
                        <input
                            class="flex w-full min-w-0 rounded-md border-neutral-300 py-1 text-sm outline-none transition-[color,box-shadow] selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:font-medium file:text-foreground file:text-xs disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-xs dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 h-auto border-0 bg-transparent! px-0 text-[16px]! leading-5 shadow-none placeholder:text-neutral-400 focus-visible:ring-0"
                            data-slot="input" type="text" placeholder="Your Company" value="{{ $listing['company_name'] }}">
                    </td>
                </tr>
                <tr class="border-border border-b">
                    <td class="border-border border-r p-4 align-top">
                        <p class="font-medium text-[16px] leading-5">Address:</p>
                        <p class="mt-2 text-[16px] leading-5">7500 W. 151st St. #23548</p>
                        <p class="mt-1 text-[16px] leading-5">Overland Park, KS 67283-3548</p>
                    </td>
                    <td class="p-4 align-top border-border border-r">
                        <p class="mb-0 font-medium text-[16px] leading-5">Address:</p>
                        <textarea
                            class="field-sizing-content flex min-h-16 w-full rounded-md border-[#D4D4D4] py-2 text-sm outline-none transition-[color,box-shadow] focus-visible:border-ring focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-xs dark:bg-input/30 dark:aria-invalid:ring-destructive/40 h-auto resize-none border-0 bg-transparent! px-0 pb-0 text-[16px]! leading-5.5 shadow-none placeholder:text-neutral-400 focus-visible:ring-0"
                            data-slot="textarea" cols="10" placeholder="Company or Home Address" rows="5">{{ $listing['address'] }}</textarea>
                    </td>
                </tr>
                <tr class="border-border border-b">
                    <td class="border-border border-r p-4 align-top">
                        <p class="text-[16px] leading-5">
                            <span class="font-medium">Print Name:</span> Susan Wos
                        </p>
                    </td>
                    <td class="flex items-center gap-2 p-4 align-top border-border border-r">
                        <span class="whitespace-nowrap font-medium text-[16px] leading-5">Print Name:</span>
                        <input
                            class="flex w-full min-w-0 rounded-md border-neutral-300 py-1 text-sm outline-none transition-[color,box-shadow] selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:font-medium file:text-foreground file:text-xs disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-xs dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 h-auto border-0 bg-transparent! px-0 text-[16px]! leading-5 shadow-none placeholder:text-neutral-400 focus-visible:ring-0"
                            data-slot="input" type="text" placeholder="Your Name" value="{{ $listing['fullname'] }}">
                    </td>
                </tr>
                <tr class="border-border border-b">
                    <td class="border-border border-r p-4 align-top">
                        <p class="text-[16px] leading-5">
                            <span class="font-medium">Print Title:</span> <!-- -->Owner
                        </p>
                    </td>
                    <td class="flex items-center gap-2 p-4 align-top border-border border-r">
                        <span class="whitespace-nowrap font-medium text-[16px] leading-5">Print Title:</span>
                        <input
                            class="flex w-full min-w-0 rounded-md border-neutral-300 py-1 text-sm outline-none transition-[color,box-shadow] selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:font-medium file:text-foreground file:text-xs disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-xs dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 h-auto border-0 bg-transparent! px-0 text-[16px]! leading-5 shadow-none placeholder:text-neutral-400 focus-visible:ring-0"
                            data-slot="input" type="text" placeholder="Company Title" value="{{ $listing['title'] }}">
                    </td>
                </tr>
                <tr>
                    <td class="border-border border-r p-4 align-top">
                        <p class="text-[16px] leading-5">
                            <span class="font-medium">Date:</span> {{ $listing['sign_date'] }}
                        </p>
                    </td>
                    <td class="p-4 align-top">
                        <p class="text-[16px] leading-5">
                            <span class="font-medium">Date:</span> {{ $listing['sign_date'] }}
                        </p>
                    </td>
                </tr>
            </table>
            <div class="space-y-8 px-4">
                <div class="space-y-4">
                    <p class="text-[16px] leading-5"><strong class="">Expiration Date of this Agreement:</strong>
                        <!-- -->This Agreement expires three hundred and sixty-five days (365) days from the later of
                        the
                        two dates above, unless an earlier date is stated in the limited purpose follows.</p>
                    <p class="text-[16px] leading-5"><strong class="">Limited Purpose:</strong> Application of
                        this Agreement is limited to the following transaction or other interactions between the
                        parties:
                    </p>
                    <p class="pl-16 text-[16px] leading-5">Participation in the disclosure of salon industry businesses
                        for
                        sale.</p>
                </div>
                <div class="space-y-4">
                    <h2 class="font-bold font-dm-sans text-[16px] leading-5">1<!-- -->. <!-- -->Definition of
                        Confidential
                        Information</h2>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">a<!-- -->.</span><strong
                                class="mr-1">
                                <!-- -->What is included.</strong>"Confidential Information" is non-public information,
                            know-how and trade secrets in any form that:</p>
                        <div class="space-y-2 pl-8">
                            <p class="text-[16px] leading-5"><span class="font-medium">i<!-- -->.</span> <!-- -->Are
                                designated in writing as "confidential" at the time of their disclosure and include (1)
                                corporate financial records or corporate proprietary information (e.g. trade secrets);
                                or
                                (2) information that a reasonable person knows or reasonably should understand to be
                                confidential and is treated as confidential by the Disclosing Party.</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">ii<!-- -->.</span> <!-- -->The
                                Disclosing Party's information or material that has or could have commercial value or
                                other
                                utility in the business in which the Disclosing Party is engaged, or other information
                                that
                                is treated as confidential by the Disclosing Party, which will include, but not be
                                limited
                                to, documents, records, information and data (whether verbal, electronic, or written),
                                drawings, models, apparatus, sketches, designs, schedules, product plans, marketing
                                plans,
                                technical procedures, or is prohibited from being disclosed for any reason pursuant to
                                law,
                                statute, regulation, ordinance, or contract.</p>
                        </div>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">b<!-- -->.</span><strong
                                class="mr-1">
                                <!-- -->What is not included.</strong>The following types of information,
                            however marked, are not Confidential Information. Information that:</p>
                        <div class="space-y-2 pl-8">
                            <p class="text-[16px] leading-5"><span class="font-medium">i<!-- -->.</span> <!-- -->Is, or
                                becomes, publicly available without a breach of this Agreement or through no fault of
                                the
                                Receiving Party;</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">ii<!-- -->.</span> <!-- -->Is, or
                                becomes lawfully known to the recipient of the information without an obligation to keep
                                it
                                confidential;</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">iii<!-- -->.</span> <!-- -->Is
                                independently developed;</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">iv<!-- -->.</span> <!-- -->Is in
                                the Receiving Party's possession before receipt from the Disclosing Party of the
                                information;</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">v<!-- -->.</span> <!-- -->Is
                                disclosed by the Receiving Party with the Disclosing Party's prior written approval; or
                            </p>
                            <p class="text-[16px] leading-5"><span class="font-medium">vi<!-- -->.</span> <!-- -->Is
                                disclosed by court order or under operation of applicable law.</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <h2 class="font-bold font-dm-sans text-[16px] leading-5">2<!-- -->. <!-- -->Obligations of Receiving
                        Party/Treatment of Confidential Information</h2>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">a<!-- -->.</span><strong
                                class="mr-1"> <!-- -->In general.</strong>Subject to the other terms of this Agreement,
                            the Receiving Party agrees except as required under any court order or law, subpoena, or any
                            other legally permitted or required disclosure, the Receiving Party agrees not to disclose
                            the
                            Disclosing Party's Confidential Information to third parties except to those employees of a
                            recipient who are required to have the information for the purposes in this Agreement,
                            Representatives, and except as otherwise allowed in this Agreement.</p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">b<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Security Precautions.</strong>The Receiving Party agrees:</p>
                        <div class="space-y-2 pl-8">
                            <p class="text-[16px] leading-5"><span class="font-medium">i<!-- -->.</span> <!-- -->To take
                                reasonable steps to protect the Disclosing Party's Confidential Information. These steps
                                must be at least as protective as those taken by the Disclosing Party to protect their
                                own
                                Confidential Information of a similar nature;</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">ii<!-- -->.</span> <!-- -->To
                                notify the other promptly upon discovery of any unauthorized use of disclosure of
                                Confidential Information; and</p>
                            <p class="text-[16px] leading-5"><span class="font-medium">iii<!-- -->.</span> <!-- -->To
                                cooperate with the other to help regain control of the Confidential Information and
                                prevent
                                further unauthorized use or disclosure of it.</p>
                        </div>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">c<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Disclosing Confidential Information if required by
                                law.</strong>The
                            Receiving Party may disclose the Disclosing Party's Confidential Information if required to
                            comply with a court order, law, or other government demand that has the force of law. The
                            Receiving Party is required to disclose the Disclosing Party's Confidential Information
                            pursuant
                            to applicable law, statute, or regulation, or court order, the Receiving Party will give
                            written
                            notice to the Disclosing Party in advance of such disclosure of the Confidential Information
                            in
                            order to provide a reasonable opportunity for the Disclosing Party to object to such
                            disclosure
                            of Confidential Information and seek a protective order or appropriate remedy. If, in the
                            absence of a protective order, the recipient determines that it is required to disclose the
                            information, it may disclose without breach of this Agreement only Confidential Information
                            specifically required and only to the extent compelled to do so.</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <h2 class="font-bold font-dm-sans text-[16px] leading-5">3<!-- -->. <!-- -->Time Periods</h2>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">a<!-- -->.</span>Except as permitted
                            above and unless otherwise required by applicable law or court order, the Receiving Party
                            will
                            not use or disclose the other Confidential Information for five years after it is received.
                            The
                            five-year period does not apply if applicable law requires a longer period. This duty to
                            withhold Confidential Information survives any expiration or termination of this Agreement
                            as
                            provided herein.</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <h2 class="font-bold font-dm-sans text-[16px] leading-5">4<!-- -->. <!-- -->General Rights and
                        Obligations</h2>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">a<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Law that applies; jurisdiction and venue.</strong>This Agreement
                            has been negotiated and executed in the United States and shall be governed by and construed
                            under the laws of the United States, without reference to conflicts of law provisions. In
                            the
                            event of any legal action to enforce or interpret this Agreement, the sole and exclusive
                            venue
                            shall be a court of competent jurisdiction located in the appropriate affiliated residences
                            or
                            locations of businesses for sale in respective Counties, and the parties each agree to and
                            do
                            hereby submit to the jurisdiction of such court. Furthermore, the parties specifically agree
                            to
                            waive any all rights to request that an action be transferred for trial to another county.
                        </p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">b<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Money damages insufficient.</strong>The Receiving Party
                            acknowledges that money damages may not be sufficient compensation for a breach of this
                            Agreement. Each of us agrees that the other may seek other orders or Confidential
                            Information
                            from becoming public in breach of this Agreement.</p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">c<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Relationship.</strong>Nothing contained in this Agreement shall be
                            deemed to constitute either party a partner, joint venture, or employee of the other party
                            for
                            any purpose.</p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">d<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Waiver.</strong>Any delay or failure of either of us to exercise a
                            right or remedy in a waiver of that, or any other, right or remedy.</p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">e<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Enforceability.</strong>If any provision of this Agreement is
                            unenforceable, the parties (or, if we cannot agree, a court) will revise it so that it can
                            be
                            enforced. If that is not possible, the rest of the agreement will remain in place.</p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">f<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Entire Agreement.</strong>This Agreement does not grant any
                            implied
                            intellectual property licenses to Confidential Information, except as stated above. We may
                            have
                            contracts with each other covering other uses of Confidential Information ("other
                            contracts").
                            The other contract may include commitments about Confidential Information, either within it
                            or
                            by referencing another non-disclosure agreement. If so, those obligations remain in place
                            for
                            purposes of that other contract. With this exception, this is the entire agreement between
                            us
                            regarding Confidential Information. It replaces all other agreements and understandings
                            regarding Confidential Information. We can only change this Agreement with a signed document
                            that states that it is changing this Agreement.</p>
                    </div>
                    <div class="space-y-2 pl-8">
                        <p class="text-[16px] leading-5"><span class="font-medium">g<!-- -->.</span><strong
                                class="mr-1"> <!-- -->Proof of Funds.</strong>Any offer provided by the potential
                            buyer(s) must be accompanied by valid proof of funds and an ability to purchase the business
                            with either a letter from your financial institution or certified accountant.</p>
                    </div>
                </div>
                <div class="space-y-6 border-border border-t pt-8">
                    <p class="text-[16px] leading-5">This Agreement and each Party's obligations shall be binding on the
                        representatives, assignees, successors of such Party. Each Party has signed this Agreement
                        through
                        its authorized representative.</p>
                    <div class="space-y-4">
                        <div class="flex max-w-lg flex-col gap-4 md:flex-row md:items-center md:gap-12">
                            <div class="whitespace-nowrap font-sans text-sm text-foreground md:text-base">Signature of
                                Receiving Party:</div>
                            <fieldset class="w-[210px]! flex-1 rounded-sm border border-border bg-white shadow-sm">
                                <legend class="ml-14 px-2 text-neutral-400 text-xs md:text-xs">Signed by:</legend>
                                <div class="px-6 pt-2 pb-6 md:px-8 md:pt-3 md:pb-8">
                                    <div class="signature text-2xl text-foreground md:text-3xl">{{ $listing['signature'] }}</div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="my-10 flex items-center gap-5 space-y-2">
                            <p class="font-medium text-[16px] leading-5">Signature of Receiving Party:</p>
                            <p class="signature text-xl text-neutral-900">Susan Wos</p>
                        </div>
                        <p class="text-[16px] leading-5"><span class="font-medium!">Typed or Printed Name:</span> <span
                                class="font-normal">Susan Wos, Founder and Lead Broker</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
