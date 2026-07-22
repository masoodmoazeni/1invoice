<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Non-Disclosure Agreement - Salonspa Connection</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Small tweaks so the PDF/print looks nicer if needed */
    body { background: #f8fafc; color: #0f172a; font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    .container { max-width: 1100px; }
    .signature { font-family: "Segoe Script", "Brush Script MT", cursive; }
    input, textarea { background: transparent; border: none; outline: none; }
    .field-border { border: 1px solid #e5e7eb; padding: 8px; border-radius: 6px; }
    /* keep page padding when printing */
    @media print {
      body { background: white; }
    }
  </style>
</head>
<body>
  <div class="container mx-auto w-full px-4 py-8 md:px-6">
    <div class="py-10 md:px-4">
      <div class="flex items-center justify-center gap-2">
        
      </div>
    </div>

    <div class="mx-auto w-full max-w-6xl pt-4 pb-10 md:px-4 bg-white shadow-sm rounded-lg">
      <div class="mb-8 text-center p-6">
        <h1 class="mb-2 font-bold text-neutral-900 text-xl leading-10 md:text-3xl">Non-Disclosure Agreement</h1>
        <p class="text-[18px] text-neutral-500 leading-5">Listing ID: <b>{{ $listing_id}}</b></p>
      </div>

      <div class="mb-8 space-y-6 px-4">
        <p class="text-[18px] leading-7">This business requires a confidentiality agreement for all buyers.</p>
        <p class="inline-block text-[18px] leading-7">
          This Non-Disclosure Agreement ("Agreement") is between Salonspa Connection ("Disclosing Party") and
          <span class="mx-2 inline-flex h-8"><b>{{ $fullname }}</b></span>
          who shall be covered by said Agreement ("Receiving Party") for the purpose of preventing the unauthorized disclosure of Confidential Information as defined below.
        </p>
      </div>

      <div class="mb-8 border border-border p-0">
        <div class="grid grid-cols-1 md:grid-cols-2 border-b">
          <div class="p-4 border-r">
            <p class="flex flex-col text-[18px] leading-5">
              <span class="font-medium">Company and Its Affiliates or Individual:</span>
              <span class="mt-1">Inform.Guide.Hire LLC DBA Salonspa Connection</span>
            </p>
          </div>
          <div class="p-4">
            <p class="font-medium text-[18px] leading-5">Company and Its Affiliates or Individual:</p>
            <b>{{$company_name}}</b>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 border-b">
          <div class="p-4 border-r">
            <p class="font-medium text-[18px] leading-5">Address:</p>
            <p class="mt-2 text-[18px] leading-5">7500 W. 151st St. #23548</p>
            <p class="mt-1 text-[18px] leading-5">Overland Park, KS 67283-3548</p>
          </div>
          <div class="p-4">
            <p class="mb-0 font-medium text-[18px] leading-5">Address:</p>
            <b>{{$address}}</b>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 border-b">
          <div class="flex items-center border-r p-4">
            <p class="text-[18px] leading-5"><span class="font-medium">Print Name:</span> Susan Wos</p>
          </div>
          <div class="flex items-center gap-2 p-4">
            <span class="whitespace-nowrap font-medium text-[18px] leading-5">Print Name:</span>
            <b>{{ $first_name }}</b>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 border-b">
          <div class="flex items-center border-r p-4">
            <p class="text-[18px] leading-5"><span class="font-medium">Print Title:</span> Owner</p>
          </div>
          <div class="flex items-center gap-2 p-4">
            <span class="whitespace-nowrap font-medium text-[18px] leading-5">Print Title:</span>
            <b>{{ $company_name}}</b>  
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2">
          <div class="flex items-center justify-between border-r p-4">
            <p class="text-[18px] leading-5"><span class="font-medium">Date:</span> 2025-12-12</p>
          </div>
          <div class="flex items-center justify-between gap-2 p-4">
            <div class="flex flex-1 items-center gap-2">
              <span class="whitespace-nowrap font-medium text-[18px] leading-5">Date:</span>
              <b>{{ $sign_date}}</b>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-8 px-4 pb-8">
        <div class="space-y-4">
          <p class="text-[18px] leading-5"><strong>Expiration Date of this Agreement:</strong> This Agreement expires three hundred and sixty-five days (365) days from the later of the two dates above, unless an earlier date is stated in the limited purpose follows.</p>
          <p class="text-[18px] leading-5"><strong>Limited Purpose:</strong> Application of this Agreement is limited to the following transaction or other interactions between the parties:</p>
          <p class="pl-16 text-[18px] leading-5">Participation in the disclosure of salon industry businesses for sale.</p>
        </div>

        <div class="space-y-4">
          <h2 class="font-bold text-[18px] leading-5">1. Definition of Confidential Information</h2>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">a.</span><strong class="mr-1"> What is included.</strong>"Confidential Information" is non-public information, know-how and trade secrets in any form that:</p>
            <div class="space-y-2 pl-8">
              <p class="text-[18px] leading-5"><span class="font-medium">i.</span> Are designated in writing as "confidential" at the time of their disclosure and include (1) corporate financial records or corporate proprietary information (e.g. trade secrets); or (2) information that a reasonable person knows or reasonably should understand to be confidential and is treated as confidential by the Disclosing Party.</p>
              <p class="text-[18px] leading-5"><span class="font-medium">ii.</span> The Disclosing Party's information or material that has or could have commercial value or other utility in the business in which the Disclosing Party is engaged, or other information that is treated as confidential by the Disclosing Party, which will include, but not be limited to, documents, records, information and data (whether verbal, electronic, or written), drawings, models, apparatus, sketches, designs, schedules, product plans, marketing plans, technical procedures, or is prohibited from being disclosed for any reason pursuant to law, statute, regulation, ordinance, or contract.</p>
            </div>
          </div>

          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">b.</span><strong class="mr-1"> What is not included.</strong>The following types of information, however marked, are not Confidential Information. Information that:</p>
            <div class="space-y-2 pl-8">
              <p class="text-[18px] leading-5"><span class="font-medium">i.</span> Is, or becomes, publicly available without a breach of this Agreement or through no fault of the Receiving Party;</p>
              <p class="text-[18px] leading-5"><span class="font-medium">ii.</span> Is, or becomes lawfully known to the recipient of the information without an obligation to keep it confidential;</p>
              <p class="text-[18px] leading-5"><span class="font-medium">iii.</span> Is independently developed;</p>
              <p class="text-[18px] leading-5"><span class="font-medium">iv.</span> Is in the Receiving Party's possession before receipt from the Disclosing Party of the information;</p>
              <p class="text-[18px] leading-5"><span class="font-medium">v.</span> Is disclosed by the Receiving Party with the Disclosing Party's prior written approval; or</p>
              <p class="text-[18px] leading-5"><span class="font-medium">vi.</span> Is disclosed by court order or under operation of applicable law.</p>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <h2 class="font-bold text-[18px] leading-5">2. Obligations of Receiving Party/Treatment of Confidential Information</h2>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">a.</span><strong class="mr-1"> In general.</strong>Subject to the other terms of this Agreement, the Receiving Party agrees except as required under any court order or law, subpoena, or any other legally permitted or required disclosure, the Receiving Party agrees not to disclose the Disclosing Party's Confidential Information to third parties except to those employees of a recipient who are required to have the information for the purposes in this Agreement, Representatives, and except as otherwise allowed in this Agreement.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">b.</span><strong class="mr-1"> Security Precautions.</strong>The Receiving Party agrees:</p>
            <div class="space-y-2 pl-8">
              <p class="text-[18px] leading-5"><span class="font-medium">i.</span> To take reasonable steps to protect the Disclosing Party's Confidential Information. These steps must be at least as protective as those taken by the Disclosing Party to protect their own Confidential Information of a similar nature;</p>
              <p class="text-[18px] leading-5"><span class="font-medium">ii.</span> To notify the other promptly upon discovery of any unauthorized use of disclosure of Confidential Information; and</p>
              <p class="text-[18px] leading-5"><span class="font-medium">iii.</span> To cooperate with the other to help regain control of the Confidential Information and prevent further unauthorized use or disclosure of it.</p>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <h2 class="font-bold text-[18px] leading-5">3. Time Periods</h2>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">a.</span>Except as permitted above and unless otherwise required by applicable law or court order, the Receiving Party will not use or disclose the other Confidential Information for five years after it is received. The five-year period does not apply if applicable law requires a longer period. This duty to withhold Confidential Information survives any expiration or termination of this Agreement as provided herein.</p>
          </div>
        </div>

        <div class="space-y-4">
          <h2 class="font-bold text-[18px] leading-5">4. General Rights and Obligations</h2>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">a.</span><strong class="mr-1"> Law that applies; jurisdiction and venue.</strong>This Agreement has been negotiated and executed in the United States and shall be governed by and construed under the laws of the United States, without reference to conflicts of law provisions. In the event of any legal action to enforce or interpret this Agreement, the sole and exclusive venue shall be a court of competent jurisdiction located in the appropriate affiliated residences or locations of businesses for sale in respective Counties, and the parties each agree to and do hereby submit to the jurisdiction of such court. Furthermore, the parties specifically agree to waive any all rights to request that an action be transferred for trial to another county.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">b.</span><strong class="mr-1"> Money damages insufficient.</strong>The Receiving Party acknowledges that money damages may not be sufficient compensation for a breach of this Agreement. Each of us agrees that the other may seek other orders or Confidential Information from becoming public in breach of this Agreement.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">c.</span><strong class="mr-1"> Relationship.</strong>Nothing contained in this Agreement shall be deemed to constitute either party a partner, joint venture, or employee of the other party for any purpose.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">d.</span><strong class="mr-1"> Waiver.</strong>Any delay or failure of either of us to exercise a right or remedy in a waiver of that, or any other, right or remedy.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">e.</span><strong class="mr-1"> Enforceability.</strong>If any provision of this Agreement is unenforceable, the parties (or, if we cannot agree, a court) will revise it so that it can be enforced. If that is not possible, the rest of the agreement will remain in place.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">f.</span><strong class="mr-1"> Entire Agreement.</strong>This Agreement does not grant any implied intellectual property licenses to Confidential Information, except as stated above. We may have contracts with each other covering other uses of Confidential Information ("other contracts"). The other contract may include commitments about Confidential Information, either within it or by referencing another non-disclosure agreement. If so, those obligations remain in place for purposes of that other contract. With this exception, this is the entire agreement between us regarding Confidential Information. It replaces all other agreements and understandings regarding Confidential Information. We can only change this Agreement with a signed document that states that it is changing this Agreement.</p>
          </div>
          <div class="space-y-2 pl-8">
            <p class="text-[18px] leading-5"><span class="font-medium">g.</span><strong class="mr-1"> Proof of Funds.</strong>Any offer provided by the potential buyer(s) must be accompanied by valid proof of funds and an ability to purchase the business with either a letter from your financial institution or certified accountant.</p>
          </div>
        </div>

        <div class="space-y-6 border-t pt-8">
          <p class="text-[18px] leading-5">This Agreement and each Party's obligations shall be binding on the representatives, assignees, successors of such Party. Each Party has signed this Agreement through its authorized representative.</p>

          <div class="space-y-4">
            <div class="flex max-w-lg flex-col gap-4 md:flex-row md:items-center md:gap-12">
              <div class="whitespace-nowrap font-sans text-base text-foreground md:text-lg">Signature of Receiving Party:</div>
              <fieldset class="w-[210px] flex-1 rounded-sm border border-border bg-white shadow-sm field-border">
                <legend class="ml-14 px-2 text-neutral-400 text-xs md:text-sm">DocuSigned by:</legend>
                <div class="px-6 pt-2 pb-6 md:px-8 md:pt-3 md:pb-8">
                  <div class="signature text-3xl md:text-4xl">{{ $signature }}</div>
                </div>
              </fieldset>
            </div>

            <div class="my-10 flex items-center gap-5 space-y-2">
              <p class="font-medium text-[18px] leading-5">Signature of Receiving Party:</p>
              <p class="signature text-2xl text-neutral-900">Susan Wos</p>
            </div>

            <p class="text-[18px] leading-5"><span class="font-medium">Typed or Printed Name:</span> <span class="font-normal">Susan Wos, Founder and Lead Broker</span></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
