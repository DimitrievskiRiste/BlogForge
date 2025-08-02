"use client";
import {Card} from "@heroui/card";
import {Skeleton} from "@heroui/react";

const LoadingInstaller = () => {
    return (
        <div className="relative flex flex-col flex-wrap gap-3 w-[100%] space-y-5">
            <Card className="w-[100%]" radius="lg">
                <Skeleton className="rounded-lg" isLoaded={false}>
                    <div className="h-10 rounded-lg bg-secondary"></div>
                </Skeleton>
            </Card>
            <div className="flex w-[100%] flex-col justify-center items-center content-center space-y-5">
                <Card className="w-[80%] md:w-[50%]" radius="lg">
                    <Skeleton className="rounded-lg" isLoaded={false}>
                        <div className="h-[34em] bg-secondary"></div>
                    </Skeleton>
                </Card>
            </div>
            <footer className="flex p-4 z-[10] w-[100%]">
                <Card className="flex relative w-[100%]">
                    <Skeleton classNames="rounded-lg" isLoaded={false}>
                        <div className="h-[3em] bg-secondary"></div>
                    </Skeleton>
                </Card>
            </footer>
        </div>
    )
}
export default LoadingInstaller;
