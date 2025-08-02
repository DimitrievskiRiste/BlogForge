"use server"
import {Suspense} from "react";
import LoadingInstaller from "@/ui/LoadingInstaller";
import InstallerTemplate from "@/ui/InstallerTemplate";
import BlogForge from "@/ui/App";
import getApiServerUrl from "@/server"

export default async function Page()
{
    const data = await fetch(getApiServerUrl+'/install/is_locked', {
        method:"GET",
    });
    return (
        <BlogForge>
            <Suspense fallback={<LoadingInstaller/>}>
                <InstallerTemplate data={data.json()}>
                    <p>Test</p>
                </InstallerTemplate>
            </Suspense>
        </BlogForge>
    )
}
