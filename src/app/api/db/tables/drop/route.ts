import getApiServerUrl from '@/server'
import {DBDropType} from "@/Types/DBDropType";
import {NextResponse} from "next/server";
export async function GET()
{
    const data = await fetch(getApiServerUrl+"/install/db/tables/drop");
    switch(data.status)
    {
        default:
            const response :DBDropType = await data.json();
            return NextResponse.json(response);
    }
}
