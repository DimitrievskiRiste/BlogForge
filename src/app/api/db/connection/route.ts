import getApiServerUrl from "@/server";
import { DBConnection } from "@/Types/DBConnection";
import {NextRequest, NextResponse} from "next/server";

export async function GET() {
    const data = await fetch(getApiServerUrl+"/install/connection");
    const d :DBConnection = await data.json();
    switch(data.status)
    {
        default:
            return NextResponse.json({data:d});
    }
}
