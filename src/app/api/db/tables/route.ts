import getApiServerUrl from '@/server'
import {NextResponse} from "next/server";
import {DBTables} from "@/Types/DBTables";
export async function GET() {
    const data = await fetch(getApiServerUrl+"/install/db/tables/check");
    switch(data.status)
    {
        default:
            const response :DBTables = await data.json();
            return NextResponse.json({data:response});
    }
}
